<?php

namespace App\Controller;

use App\Controller\Trait\ControllerTrait;
use App\Entity\Faq;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{
    use ControllerTrait;

    /**
     * @return Response
     */
    #[Route('/faq', name: 'faq')]
    public function index(): Response
    {
        $entries = $this->em->getRepository(Faq::class)->findBy([
            'deleted_at' => null,
            'visible' => true,
        ], [
            'id' => 'desc',
        ]);

        return $this->render('faq/index.html.twig', [
            'entries' => $entries,
        ]);
    }
}
