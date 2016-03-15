<?php

namespace Tests\Functional\Printer;

use Paraunit\Lifecycle\EngineEvent;
use Paraunit\Printer\ConsoleFormatter;
use Paraunit\Output\OutputContainerInterface;
use Paraunit\TestResult\TestResultContainer;
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
    public function testOnEngineStartHasAllTagsRegistered($containerServiceName)
    {
        /** @var TestResultContainer $testResultContainer */
        $testResultContainer = $this->container->get($containerServiceName);
        /** @var ConsoleFormatter $consoleFormatter */
        $consoleFormatter = $this->container->get('paraunit.printer.console_formatter');
        $outputInterface = new BufferedOutput();
        $event = new EngineEvent($outputInterface);

        $consoleFormatter->onEngineStart($event);

        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $formatter = $outputInterface->getFormatter();
        $style = $formatter->getStyle($tag);
        $this->assertInstanceOf(
            'Symfony\Component\Console\Formatter\OutputFormatterStyleInterface',
            $style,
            'Missing tag style: ' . $tag . ' -- service ' .$containerServiceName
        );
    }

    public function serviceTagsProvider()
    {
        return array(
//            array('paraunit.test_result.null_container'),
            array('paraunit.test_result.abnormal_terminated_container'),
            array('paraunit.test_result.error_container'),
            array('paraunit.test_result.failure_container'),
            array('paraunit.test_result.warning_container'),
            array('paraunit.test_result.risky_container'),
            array('paraunit.test_result.skipped_container'),
            array('paraunit.test_result.incomplete_container'),
        );
    }
}
