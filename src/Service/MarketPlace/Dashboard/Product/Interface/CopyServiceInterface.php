<?php

namespace App\Service\MarketPlace\Dashboard\Product\Interface;

interface CopyServiceInterface
{
    public function copyProduct(int $id): void;
}