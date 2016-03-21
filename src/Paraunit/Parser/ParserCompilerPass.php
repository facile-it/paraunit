<?php

namespace Paraunit\Parser;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ParserCompilerPass
 */
class ParserCompilerPass implements CompilerPassInterface
{

    const TAGGED_SERVICES = 'log_parser';
    const TO_COMPILE      = 'paraunit.parser.json_log_parser';

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {

        $taggedServices = $container->findTaggedServiceIds(self::TAGGED_SERVICES);

        uasort(
            $taggedServices,
            function ($a, $b) {
                return $a[0]['priority'] > $b[0]['priority'];
            }
        );

        foreach ($taggedServices as $id => $taggedService) {
            $service = $container->getDefinition(self::TO_COMPILE);
            $service->addMethodCall('addParser', array(new Reference($id)));
        }

    }

}
