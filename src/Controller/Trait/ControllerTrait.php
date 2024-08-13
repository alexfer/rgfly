<?php

namespace App\Controller\Trait;

use Doctrine\ORM\EntityManagerInterface;

trait ControllerTrait
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}