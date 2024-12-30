<?php declare(strict_types=1);

namespace Inno\Service\MarketPlace\Store\Order\Interface;

interface ComputeInterface
{
    /**
     * @param array $input
     * @return void
     */
    public function process(array $input): void;
}