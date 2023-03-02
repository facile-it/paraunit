<?php

declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Runner\Runner;
use PHPUnit\Framework\Attributes\Medium;
use Tests\BaseIntegrationTestCase;

#[Medium]
class RunnerWithCoverageTest extends BaseIntegrationTestCase
{
    public function testAllGreen(): void
    {
        $this->configuration = new CoverageConfiguration(true);

        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();
        /** @var Runner $runner */
        $runner = $this->getService(Runner::class);

        $exitCode = $runner->run();

        $output = $this->getConsoleOutput();
        $this->assertEquals(0, $exitCode, $output->getOutput());
        $this->assertStringNotContainsString('COVERAGE NOT FETCHED', $output->getOutput());

        $resultsOnNewline = PHP_OS_FAMILY !== 'Linux'
            ? '...'
            : PHP_EOL . '...';

        $this->assertOutputOrder($this->getConsoleOutput(), [
            'PARAUNIT',
            'Coverage driver in use',
            $resultsOnNewline,
        ]);
    }
}
