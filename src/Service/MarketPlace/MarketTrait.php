<?php

namespace App\Service\MarketPlace;

use App\Entity\MarketPlace\Market;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

trait MarketTrait
{
    /**
     * @param Request $request
     * @param UserInterface $user
     * @param EntityManagerInterface $em
     * @return Market|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function market(
        Request                $request,
        UserInterface          $user,
        EntityManagerInterface $em,
    ): ?Market
    {
        $market = $em->getRepository(Market::class)
            ->findOneBy($this->criteria($user, [
                'id' => $request->get('market')
            ], 'owner'));

        if (!$market) {
            throw $this->createAccessDeniedException();
        }
        return $market;
    }

}