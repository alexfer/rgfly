<?php

namespace App\Service\MarketPlace;

use App\Entity\MarketPlace\Store;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

trait StoreTrait
{
    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Store|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function store(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): ?Store
    {
        $market = $em->getRepository(Store::class)
            ->findOneBy($this->criteria($user, [
                'id' => $request->get('store')
            ], 'owner'));

        if (!$market) {
            throw $this->createAccessDeniedException();
        }
        return $market;
    }

}