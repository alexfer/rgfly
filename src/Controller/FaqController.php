<?php

namespace App\Controller;

use App\Entity\Faq;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{

    /**
     *
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/faq', name: 'faq')]
    public function index(EntityManagerInterface $em): Response
    {
        $entries = $em->getRepository(Faq::class)->findBy([
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
