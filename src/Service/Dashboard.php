<?php

namespace App\Service;

use App\Entity\Entry;
use App\Repository\EntryRepository;
use Symfony\Component\Security\Core\User\UserInterface;

trait Dashboard
{

    /**
     * 
     * @var EntryRepository
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
     * 
     * @param UserInterface $user
     * @param array|null $criteria
     * @return array
     */
    private function criteria(UserInterface $user, ?array $criteria = null): array
    {
        $securityContext = $this->container->get('security.authorization_checker');

        $options = [            
            'user_id' => $user->getId(),
        ];

        if ($criteria && \count($criteria)) {
            $options = \array_merge($options, $criteria);
        }

        if ($securityContext->isGranted('ROLE_ADMIN')) {
            unset($options['user_id']);
        }

        return $options;
    }

    /**
     * 
     * @return array
     */
    public function build(UserInterface $user): array
    {
        $navbar = $childrens = $count = [];
        foreach (\array_flip(Entry::TYPE) as $key => $class) {
            $class = sprintf('\App\Controller\Dashboard\%sController', $class);
            if (\class_exists($class)) {

                $navbar[$key] = $class;
                $childrens[$key] = $class::CHILDRENS[$key];
                $count[$key] = $this->repository->count($this->criteria($user, [
                    'type' => $key,
                    'deleted_at' => null,
                ]));
            }
        }

        return [
            'navbar' => $navbar,
            'childrens' => $childrens,
            'count' => $count,
        ];
    }
}
