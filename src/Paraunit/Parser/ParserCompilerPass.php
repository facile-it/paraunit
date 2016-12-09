<?php

namespace Paraunit\Parser;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ParserCompilerPass
 * @package Paraunit\Parser
 */
class ParserCompilerPass implements CompilerPassInterface
{
    const TAGGED_SERVICES = 'log_parser';
    const LOG_PARSER_SERVICE = 'paraunit.parser.json_log_parser';
    const TEST_RESULT_LIST_SERVICE = 'paraunit.test_result.test_result_list';

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

        $jsonLogService = $container->getDefinition(self::LOG_PARSER_SERVICE);
        $listService = $container->getDefinition(self::TEST_RESULT_LIST_SERVICE);

        foreach ($taggedServices as $id => $taggedService) {
            $callParameters = array(new Reference($id));
            $jsonLogService->addMethodCall('addParser', $callParameters);
            $listService->addMethodCall('addParser', $callParameters);
        }
    }
}
