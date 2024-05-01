<?php

namespace App;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Override;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{

    use MicroKernelTrait;

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    #[Override]
    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new class() implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                date_default_timezone_set($container->getParameter('default_timezone'));
                $container->getDefinition('doctrine.orm.default_configuration')
                    ->addMethodCall(
                        'setIdentityGenerationPreferences', [
                            [
                                PostgreSQLPlatform::class => ClassMetadataInfo::GENERATOR_TYPE_SEQUENCE,
                            ],
                        ]
                    );
            }
        });
    }
}
