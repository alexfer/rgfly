<?php

namespace App\Service;

use App\Entity\Entry;
use App\Repository\EntryRepository;

trait DashboardNavbar
{

    /**
     * 
     * @var EntryRepository
     */
    private EntryRepository $repository;

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
     * @return array
     */
    public function build(): array
    {
        $navbar = $childrens = $count = [];
        foreach (\array_flip(Entry::TYPE) as $key => $class) {
            $class = sprintf('\App\Controller\Dashboard\%sController', $class);
            if (\class_exists($class)) {

                $navbar[$key] = $class;
                $childrens[$key] = $class::CHILDRENS[$key];
                $count[$key] = $this->repository->count([
                    'type' => $key,
                    'deleted_at' => null,
                    ]);
            }
        }

        return [
            'navbar' => $navbar,
            'childrens' => $childrens,
            'count' => $count,
        ];
    }
}
