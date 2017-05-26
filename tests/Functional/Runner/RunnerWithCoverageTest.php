<?php
declare(strict_types=1);

namespace Tests\Functional\Runner;

use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Runner\Runner;
use Tests\BaseIntegrationTestCase;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class RunnerWithCoverageTest
 * @package Tests\Functional\Runner
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
        $outputInterface = new UnformattedOutputStub();
        $this->setTextFilter('ThreeGreenTestStub.php');
        $this->loadContainer();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $this->assertEquals(0, $runner->run($outputInterface));
        $this->assertOutputOrder($outputInterface, [
            'PARAUNIT',
            'Coverage driver in use',
            '...',
        ]);
    }
}
