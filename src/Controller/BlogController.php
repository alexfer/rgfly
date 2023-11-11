<?php

namespace App\Controller;

use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('', name: 'app_blog')]
    public function index(Request $request, EntryRepository $repository): Response
    {
        $entries = $repository->findBy(['type' => 'blog'], ['id' => 'desc'], 6);

        return $this->render('blog/index.html.twig', [
            'entries' => $entries,
        ]);
    }

    #[Route('/{slug}', name: 'app_blog_view')]
    public function view(Request $request, EntryRepository $repository) {

        $entry = $repository->findOneBy(['slug' => $request->get('slug')]);

        return $this->render('blog/view.html.twig', [
            'entry' => $entry,
        ]);
    }
}
