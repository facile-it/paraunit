<?php

namespace Tests\Unit\Bin;

use Paraunit\Bin\Paraunit;
use Tests\BaseUnitTestCase;

/**
 * Class ParaunitTest
 * @package Tests\Unit\Bin
 */
class ParaunitTest extends BaseUnitTestCase
{
    public function testCreateApplication()
    {
        $application = Paraunit::createApplication();

        $this->assertInstanceOf('Symfony\Component\Console\Application', $application);
        $this->assertInstanceOf('Paraunit\Command\ParallelCommand', $application->find('run'));
        $this->assertInstanceOf('Paraunit\Command\CoverageCommand', $application->find('coverage'));
    }
}
