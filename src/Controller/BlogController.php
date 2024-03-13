<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/blog')]
class BlogController extends AbstractController
{

    /**
     * @param Request $request
     * @param EntryRepository $repository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    #[Route('', name: 'app_blog')]
    #[Route('/category/{slug}', name: 'app_blog_category')]
    #[Route('/date/{date}', name: 'app_blog_date')]
    public function index(
        Request            $request,
        EntryRepository    $repository,
        CategoryRepository $categoryRepository,
    ): Response
    {
        $slug = $request->get('slug');
        $entries = $repository->findEntriesByCondition($slug, 'blog', 12);
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        $categories = $categoryRepository->findBy([], ['slug' => 'asc']);

        return $this->render('blog/index.html.twig', [
            'entries' => $entries,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     * @param EntryRepository $repository
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/{slug}', name: 'app_blog_view')]
    public function view(
        Request             $request,
        EntryRepository     $repository,
        TranslatorInterface $translator,
    ): Response
    {
        $entry = $repository->findOneBy(['slug' => $request->get('slug')]);

        if (is_null($entry)) {
            throw $this->createNotFoundException($translator->trans('http_error_404.description'));
        }

        return $this->render('blog/view.html.twig', [
            'entry' => $entry,
        ]);
    }
}
