<?php

namespace App\Service\MarketPlace;

use App\Entity\MarketPlace\Store;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

trait StoreTrait
{
    /**
     * @param UserInterface $user
     * @param string $key
     * @param array|null $criteria
     * @return UserInterface[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function criteria(UserInterface $user, ?array $criteria = null, string $key = 'user'): array
    {
        $securityContext = $this->container->get('security.authorization_checker');

        $options = [
            $key => $user,
        ];

        if ($criteria && count($criteria)) {
            $options = array_merge($options, $criteria);
        }

        if ($securityContext->isGranted('ROLE_ADMIN')) {
            unset($options[$key]);
        }

        return $options;
    }

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
        $store = $em->getRepository(Store::class)
            ->findOneBy($this->criteria($user, [
                'id' => $request->get('store')
            ], 'owner'));

        if (!$store) {
            throw $this->createAccessDeniedException();
        }
        return $store;
    }

}