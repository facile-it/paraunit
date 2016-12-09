<?php

namespace Tests\Functional\Runner;

use Paraunit\Configuration\ParallelCoverageConfiguration;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Runner\Runner;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\UnformattedOutputStub;

/**
 * Class RunnerWithCoverageTest
 * @package Tests\Functional\Runner
 */
class RunnerWithCoverageTest extends BaseFunctionalTestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->configuration = new ParallelCoverageConfiguration();
    }

    public function testAllGreen()
    {
        $outputInterface = new UnformattedOutputStub();

        /** @var Runner $runner */
        $runner = $this->container->get('paraunit.runner.runner');

        $fileArray = array('tests/Stub/ThreeGreenTestStub.php');

        $this->assertEquals(0, $runner->run($fileArray, $outputInterface, new PHPUnitConfig('')));
        $this->assertOutputOrder($outputInterface, array(
            'PARAUNIT',
            'Coverage driver in use',
            '...',
        ));
    }
}
