<?php

declare(strict_types=1);

namespace App\Controller\Trait;

use App\Entity\MarketPlace\StoreCustomer;
use Doctrine\ORM\EntityManagerInterface;

trait ControllerTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $user
     * @return StoreCustomer|null
     */
    protected function getCustomer($user): ?StoreCustomer
    {
        return $this->em->getRepository(StoreCustomer::class)->findOneBy(['member' => $user]);
    }
}