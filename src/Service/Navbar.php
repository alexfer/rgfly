<?php

namespace App\Service;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait Navbar
{

    /**
     *
     * @var EntryRepository|null
     */
    private ?EntryRepository $repository = null;

    /**
     *
     * @param EntryRepository $repository
     */
    public function __construct(EntryRepository $repository)
    {
        $this->repository = $repository;
    }

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
     * @param UserInterface $user
     * @return array[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function build(UserInterface $user): array
    {
        $navbar = $children = $count = [];
        foreach (array_flip(Entry::TYPE) as $key => $class) {
            $class = sprintf('\App\Controller\Dashboard\%sController', $class);
            if (class_exists($class)) {

                $navbar[$key] = $class;
                $children[$key] = $class::CHILDREN[$key];
                $count[$key] = $this->repository->count($this->criteria($user, [
                    'type' => $key,
                    'deleted_at' => null,
                ]));
            }
        }

        return [
            'navbar' => $navbar,
            'children' => $children,
            'count' => $count,
        ];
    }
}
