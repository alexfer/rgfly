<?php

namespace App\Controller\Dashboard\MarketPlace\Market;

use App\Entity\MarketPlace\Market;
use App\Form\Type\Dashboard\MarketPlace\MarketType;
use App\Repository\MarketPlace\MarketRepository;
use App\Security\Voter\MarketVoter;
use App\Service\FileUploader;
use App\Service\Dashboard;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/marker-place')]
class MarketController extends AbstractController
{
    use Dashboard;

    /**
     * @param UserInterface $user
     * @param MarketRepository $marketRepository
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/market', name: 'app_dashboard_market_place_market')]
    public function index(
        UserInterface    $user,
        MarketRepository $marketRepository,
    ): Response
    {
        $criteria = $this->criteria($user, null, 'owner');
        $markets = $marketRepository->findBy($criteria, ['created_at' => 'desc']);

        return $this->render('dashboard/content/market_place/market/index.html.twig', $this->navbar() + [
                'markets' => $markets,
            ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserInterface $user
     * @param SluggerInterface $slugger
     * @param TranslatorInterface $translator
     * @param ParameterBagInterface $params
     * @return Response
     * @throws Exception
     */
    #[Route('/create', name: 'app_dashboard_market_place_create_market')]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
        UserInterface          $user,
        SluggerInterface       $slugger,
        TranslatorInterface    $translator,
        ParameterBagInterface  $params,
    ): Response
    {
        $market = new Market();
        $form = $this->createForm(MarketType::class, $market);

        $markets = $user->getMarkets()->count();

        if (!$markets) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $market->setOwner($user)
                    ->setSlug($slugger->slug($form->get('name')->getData())->lower());

                $file = $form->get('logo')->getData();

                if ($file) {
                    $fileUploader = new FileUploader($this->getTargetDir($params, $market->getId()), $slugger, $em);

                    try {
                        $attach = $fileUploader->upload($file)->handle($market);
                    } catch (Exception $ex) {
                        throw new Exception($ex->getMessage());
                    }

                    $market->setAttach($attach);
                }

                $em->persist($market);
                $em->flush();

                $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.created')]));

                return $this->redirectToRoute('app_dashboard_market_place_edit_market', ['id' => $market->getId()]);
            }
        } else {
            throw new AccessDeniedHttpException('Permission denied.');
        }

        return $this->render('dashboard/content/market_place/market/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param Market $market
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param TranslatorInterface $translator
     * @param ParameterBagInterface $params
     * @param CacheManager $cacheManager
     * @return Response
     * @throws Exception
     */
    #[Route('/edit/{id}', name: 'app_dashboard_market_place_edit_market', methods: ['GET', 'POST'])]
//    #[IsGranted(MarketVoter::EDIT, subject: 'entry', statusCode: Response::HTTP_FORBIDDEN)]
    public function edit(
        Request                $request,
        Market                 $market,
        EntityManagerInterface $em,
        SluggerInterface       $slugger,
        TranslatorInterface    $translator,
        ParameterBagInterface  $params,
        CacheManager           $cacheManager,
    ): Response
    {
        $form = $this->createForm(MarketType::class, $market);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('logo')->getData();

            if ($file) {
                $fileUploader = new FileUploader($this->getTargetDir($params, $market->getId()), $slugger, $em);

                $oldAttach = $market->getAttach();

                if ($oldAttach) {
                    $em->remove($oldAttach);
                    $market->setAttach(null);

                    $fs = new Filesystem();
                    $oldFile = $this->getTargetDir($params, $market->getId()) . '/' . $oldAttach->getName();

                    $cacheManager->remove($oldFile, 'market_thumb');

                    if ($fs->exists($oldFile)) {
                        $fs->remove($oldFile);
                    }
                }

                try {
                    $attach = $fileUploader->upload($file)->handle($market);
                } catch (Exception $ex) {
                    throw new Exception($ex->getMessage());
                }

                $market->setAttach($attach);
            }

            $em->persist($market);
            $em->flush();

            $this->addFlash('success', json_encode(['message' => $translator->trans('user.entry.updated')]));

            return $this->redirectToRoute('app_dashboard_market_place_edit_market', ['id' => $market->getId()]);
        }

        return $this->render('dashboard/content/market_place/market/_form.html.twig', $this->navbar() + [
                'form' => $form,
            ]);
    }

    /**
     * @param Request $request
     * @param Market $market
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    #[Route('/delete/{id}', name: 'app_dashboard_delete_market', methods: ['POST'])]
    public function delete(
        Request                $request,
        Market                 $market,
        EntityManagerInterface $em,
    ): Response
    {
        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
            $products = $market->getProducts();
            $date = new DateTime('@' . strtotime('now'));
            foreach ($products as $product) {
                $product->setDeletedAt($date);
                $em->persist($product);
            }
            $market->setDeletedAt($date);
            $em->persist($market);
            $em->flush();
        }

        return $this->redirectToRoute('app_dashboard_market_place_market');
    }

    /**
     *
     * @param Market $market
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/restore/{id}', name: 'app_dashboard_restore_market')]
    public function restore(
        Market                 $market,
        EntityManagerInterface $em,
    ): Response
    {
        $products = $market->getProducts();
        foreach ($products as $product) {
            $product->setDeletedAt(null);
            $em->persist($product);
        }

        $market->setDeletedAt(null);
        $em->persist($market);
        $em->flush();

        return $this->redirectToRoute('app_dashboard_market_place_market');
    }

    /**
     * @param ParameterBagInterface $params
     * @param int $id
     * @return string
     */
    private function getTargetDir(ParameterBagInterface $params, int $id): string
    {
        return sprintf('%s/%d', $params->get('market_storage_logo'), $id);
    }

}
