<?php

namespace App\Controller\MarketPlace;

use App\Entity\MarketPlace\StoreCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(StoreCategory::class)->findBy(['parent' => null]);
        return $this->render('market_place/search/categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}