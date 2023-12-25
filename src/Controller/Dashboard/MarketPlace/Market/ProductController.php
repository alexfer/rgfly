<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\Market;
use App\Entity\MarketPlace\MarketCategory;
use App\Entity\MarketPlace\MarketCategoryProduct;
use App\Entity\MarketPlace\MarketProduct;
use App\Form\Type\Dashboard\MarketPlace\ProductType;
use App\Security\Voter\ProductVoter;
use App\Service\Navbar;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/market-place/product')]
class ProductController extends AbstractController
{
    use Navbar;

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/{market}', name: 'app_dashboard_market_place_market_product')]
    public function index(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): Response
    {
        $criteria = $this->criteria($user, ['id' => $request->get('market')], 'owner');
        // TODO: check in future
        $market = $em->getRepository(Market::class)->findOneBy($criteria, ['id' => 'desc']);
        $entries = $em->getRepository(MarketProduct::class)->findBy(['market' => $market], ['id' => 'desc']);

        return $this->render('dashboard/content/market_place/product/index.html.twig', $this->build($user) + [
                'market' => $market,
                'entries' => $entries,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param MarketProduct $entry
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @param SluggerInterface $slugger
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/edit/{market}/{id}', name: 'app_dashboard_market_place_edit_product', methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::EDIT, subject: 'entry', statusCode: Response::HTTP_FORBIDDEN)]
    public function edit(
        Request                $request,
        UserInterface          $user,
        MarketProduct          $entry,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
        SluggerInterface       $slugger,
    ): Response
    {
        $form = $this->createForm(ProductType::class, $entry);
        $form->handleRequest($request);

        $categoryRepository = $em->getRepository(MarketCategory::class);
        $categories = $categoryRepository->findBy([], ['name' => 'asc']);
        $repository = $em->getRepository(MarketCategoryProduct::class);

        $uniqueError = null;
        $name = $form->get('name')->getData();

        if ($name) {
            try {
                $entry->setSlug($slugger->slug($name)->lower());
                $em->persist($entry);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $uniqueError = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.title'),
                    '%value%' => $name,
                ], 'validators');
            }
        }

        if ($form->isSubmitted() && $form->isValid() && !$uniqueError) {
            $requestCategory = $request->get('category');
            if ($requestCategory) {
                $repository->removeCategoryProduct($entry);
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new MarketCategoryProduct();
                    $entryCategory->setProduct($entry)
                        ->setCategory($categoryRepository->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            } else {
                $repository->removeCategoryProduct($entry);
            }
            $em->persist($entry);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_product', [
                'market' => $request->get('market'),
                'id' => $entry->getId(),
            ]);
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->build($user) + [
                'form' => $form,
                'error' => $uniqueError,
                'entry' => $entry,
                'categories' => $categories,
            ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/create/{market}', name: 'app_dashboard_market_place_create_product', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        UserInterface          $user,
        SluggerInterface       $slugger,
        EntityManagerInterface $em,
        TranslatorInterface    $translator,
    ): Response
    {
        $categories = $em->getRepository(MarketCategory::class)->findBy([], ['name' => 'asc']);
        $market = $em->getRepository(Market::class)->findOneBy($this->criteria($user, ['id' => $request->get('market')], 'owner'));

        $entry = new MarketProduct();

        $form = $this->createForm(ProductType::class, $entry);
        $form->handleRequest($request);

        $uniqueError = null;

        if ($form->isSubmitted() && $form->isValid()) {

            $name = $form->get('name')->getData();
            $slug = $slugger->slug($name)->lower();

            $requestCategory = $request->get('category');

            if ($requestCategory) {
                foreach ($requestCategory as $key => $value) {
                    $entryCategory = new MarketCategoryProduct();
                    $entryCategory->setProduct($entry)
                        ->setCategory($em->getRepository(MarketCategory::class)->findOneBy(['id' => $key]));
                    $em->persist($entryCategory);
                }
            }

            try {
                $entry->setSlug($slug)->setMarket($market);
                $em->persist($entry);
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $uniqueError = $translator->trans('slug.unique', [
                    '%name%' => $translator->trans('label.form.product_name'),
                    '%value%' => $name,
                ], 'validators');
            }

            if (!$uniqueError) {
                $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));
                return $this->redirectToRoute('app_dashboard_market_place_edit_product', ['market' => $request->get('market'), 'id' => $entry->getId()]);
            }
        }

        return $this->render('dashboard/content/market_place/product/_form.html.twig', $this->build($user) + [
                'form' => $form,
                'error' => $uniqueError,
                'entry' => $entry,
                'categories' => $categories,
            ]);
    }
}
