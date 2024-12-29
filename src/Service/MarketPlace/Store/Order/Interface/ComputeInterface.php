<?php declare(strict_types=1);

namespace Essence\Service\MarketPlace\Store\Order\Interface;

interface ComputeInterface
{
    /**
     * @param array $input
     * @return void
     */
    public function process(array $input): void;
}