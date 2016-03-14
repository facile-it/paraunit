<?php

namespace Tests\Functional\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Printer\ConsoleFormatter;
use Paraunit\Output\OutputContainerInterface;
use Tests\BaseFunctionalTestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class ConsoleFormatterTest
 * @package Tests\Functional\Printer
 */
class ConsoleFormatterTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider serviceTagsProvider
     */
    public function testOnEngineStartHasAllTagsRegistered($outputContainerServiceName)
    {
        /** @var OutputContainerInterface $outputContainer */
        $outputContainer = $this->container->get($outputContainerServiceName);
        /** @var ConsoleFormatter $consoleFormatter */
        $consoleFormatter = $this->container->get('paraunit.printer.console_formatter');
        $outputInterface = new BufferedOutput();
        $event = new EngineEvent($outputInterface);

        $consoleFormatter->onEngineStart($event);

        $formatter = $outputInterface->getFormatter();
        $style = $formatter->getStyle($outputContainer->getTag());
        $this->assertInstanceOf(
            'Symfony\Component\Console\Formatter\OutputFormatterStyleInterface',
            $style,
            'Missing tag style: ' . $outputContainer->getTag() . ' -- service ' .$outputContainerServiceName
        );
    }

    public function serviceTagsProvider()
    {
        return array(
//            array('paraunit.output.null_container'),
            array('paraunit.output.abnormal_terminated_container'),
            array('paraunit.output.error_container'),
            array('paraunit.output.failure_container'),
            array('paraunit.output.warning_container'),
            array('paraunit.output.risky_container'),
            array('paraunit.output.skipped_container'),
            array('paraunit.output.incomplete_container'),
        );
    }
}
