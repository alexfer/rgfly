<?php

namespace App\Service\MarketPlace\Dashboard\Operation\Handler;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JsonFactory
{
    public CacheManager $cacheManager;

    /**
     * @var ParameterBagInterface
     */
    public ParameterBagInterface $params;

    public function __construct()
    {

    }
}