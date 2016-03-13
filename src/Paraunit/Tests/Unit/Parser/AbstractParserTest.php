<?php

namespace Paraunit\Tests\Unit\Parser;

use Paraunit\Parser\AbstractParser;
use Paraunit\Printer\OutputContainer;
use Paraunit\Tests\BaseUnitTestCase;
use Paraunit\Tests\Stub\StubbedParaProcess;

/**
 * Class AbstractParserTest
 * @package Paraunit\Tests\Unit\Parser
 */
class AbstractParserTest extends BaseUnitTestCase
{
    public function testParsingFoundResultIncludesFunctionNameInOutputContainer()
    {
        $container = new OutputContainer('tag', 'title');
        $parser = new AbstractParser($container, 'e', 'error');
        $log = $this->getLogWithStatus('error');

        $parser->parsingFoundResult(new StubbedParaProcess(), $log);

        $this->assertNotEmpty($container->getOutputBuffer(), 'No output generated');
        $outputBuffer = $container->getOutputBuffer(); // PHP 5.3 crap
        $finalResult = array_pop($outputBuffer); // PHP 5.3 crap, again
        $finalResult = $finalResult[0];

        $this->assertContains('Paraunit\Tests\Stub\ThreeGreenTestStub::testGreenTwo', $finalResult);
        $this->assertContains('Undefined variable: asd', $finalResult);
        $this->assertContains('/home/paraunit/projects/src/Paraunit/Tests/Stub/ThreeGreenTestStub.php:18', $finalResult);
    }
}
