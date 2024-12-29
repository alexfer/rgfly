<?php declare(strict_types=1);

namespace Essence\Service\MarketPlace\Dashboard\Operation\FactoryHandler;

use Essence\Service\MarketPlace\Dashboard\Operation\Interface\HandleFactoryInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class JsonFactory implements HandleFactoryInterface
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