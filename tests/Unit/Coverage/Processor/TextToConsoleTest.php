<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Coverage\Processor\TextToConsole;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class TextToConsoleTest
 * @package Tests\Unit\Proxy
 */
class TextToConsoleTest extends BaseUnitTestCase
{
    public function testWriteToConsole()
    {
        $output = new UnformattedOutputStub();
        $text = new TextToConsole($output, true);

        $text->process($this->createCodeCoverage());

        $this->assertContains('Code Coverage Report', $output->getOutput());
    }
}
