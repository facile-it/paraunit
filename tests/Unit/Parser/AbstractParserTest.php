<?php

namespace Tests\Unit\Parser;

use Paraunit\Parser\AbstractParser;
use Paraunit\Output\OutputContainer;
use Tests\BaseUnitTestCase;
use Tests\Stub\StubbedParaProcess;

/**
 * Class AbstractParserTest
 * @package Tests\Unit\Parser
 */
class AbstractParserTest extends BaseUnitTestCase
{
    public function testParsingFoundResult()
    {
        $container = new OutputContainer('tag', 'title', 'e');
        $parser = new AbstractParser($container, 'error');
        $log = $this->getLogWithStatus('error');

        $parser->parsingFoundResult(new StubbedParaProcess(), $log);

        $this->assertCOnlyNotEmpty($container->getOutputBuffer(), 'No output generated');
        $outputBuffer = $container->getOutputBuffer(); // PHP 5.3 crap
        $finalResult = array_pop($outputBuffer); // PHP 5.3 crap, again
        $finalResult = $finalResult[0];

        $this->assertContains('Paraunit\Tests\Stub\ThreeGreenTestStub::testGreenTwo', $finalResult);
        $this->assertContains('Undefined variable: asd', $finalResult);
        $this->assertContains('/home/paraunit/projects/src/Paraunit/Tests/Stub/ThreeGreenTestStub.php:18', $finalResult);
    }
}
