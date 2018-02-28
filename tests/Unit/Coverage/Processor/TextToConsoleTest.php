<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Coverage\Processor\TextSummary;
use Paraunit\Proxy\Coverage\CodeCoverage;
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
        $text = new TextSummary($output, true);

        $text->process(new CodeCoverage());

        $this->assertContains('Code Coverage Report', $output->getOutput());
    }
}
