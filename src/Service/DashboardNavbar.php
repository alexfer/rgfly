<?php

namespace App\Service;

use App\Entity\Entry;

class DashboardNavbar
{

    /**
     * 
     * @return array
     */
    public static function build(): array
    {
        $navbar = $childrens = [];

        foreach (\array_flip(Entry::TYPE) as $key => $class) {
            $class = sprintf('\App\Controller\Dashboard\%sController', $class);
            if (\class_exists($class)) {
                $navbar[$key] = $class;
                $childrens[$key] = $class::CHILDRENS[$key];
            }
        }

        return [
            'navbar' => $navbar,
            'childrens' => $childrens,
        ];
    }
}
