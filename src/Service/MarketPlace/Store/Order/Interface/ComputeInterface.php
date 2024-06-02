<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

interface ComputeInterface
{
    public function process(array $input): void;
}