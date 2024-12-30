<?php

namespace Inno\Service\MarketPlace\Dashboard\Product\Interface;

interface CopyServiceInterface
{
    public function copyProduct(int $id): void;
}