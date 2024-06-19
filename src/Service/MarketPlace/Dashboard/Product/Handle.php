<?php

namespace App\Service\MarketPlace\Dashboard\Product;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request, RequestStack};

abstract class Handle
{

    /**
     * @var Request|null
     */
    protected ?Request $request;

    /**
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     */
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected RequestStack                    $requestStack
    )
    {
        $this->request = $this->requestStack->getCurrentRequest();
    }

}