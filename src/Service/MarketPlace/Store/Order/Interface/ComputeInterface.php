<?php

namespace App\Service\MarketPlace\Store\Order\Interface;

interface ComputeInterface
{
    /**
     * @param array $input
     * @return void
     */
    public function process(array $input): void;
}