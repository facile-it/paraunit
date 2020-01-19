<?php

declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Runner\Runner;
use Tests\BaseIntegrationTestCase;

/**
 * @medium
 */
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
        $this->assertNotContains('COVERAGE NOT FETCHED', $output->getOutput());
        $this->assertOutputOrder($this->getConsoleOutput(), [
            'PARAUNIT',
            'Coverage driver in use',
            '...',
        ]);
    }
}
