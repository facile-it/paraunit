<?php

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Text;
use Paraunit\Coverage\Processor\TextToConsole;
use Paraunit\Proxy\Coverage\CodeCoverage;
use Tests\BaseUnitTestCase;

/**
 * Class TextToConsoleTest
 * @package Tests\Unit\Proxy
 */
class TextToConsoleTest extends BaseUnitTestCase
{
    public function testWriteToConsole()
    {
        $text = new TextToConsole();

        $this->expectOutputString('');

        $text->process(new CodeCoverage());
    }
}
