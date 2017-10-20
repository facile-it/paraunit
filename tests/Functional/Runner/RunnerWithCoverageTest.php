<?php
declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Runner\Runner;
use Tests\BaseIntegrationTestCase;

/**
 * Class RunnerWithCoverageTest
 * @package Tests\Functional\Runner
 * @medium
 */
class RunnerWithCoverageTest extends BaseIntegrationTestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->configuration = new CoverageConfiguration();
    }

    public function testAllGreen()
    {
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();
        /** @var Runner $runner */
        $runner = $this->container->get(Runner::class);

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
