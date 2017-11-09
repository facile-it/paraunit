<?php

declare(strict_types=1);

namespace Tests\Unit\Bin;

use Paraunit\Bin\Paraunit;
use Paraunit\Command\CoverageCommand;
use Paraunit\Command\ParallelCommand;
use Symfony\Component\Console\Application;
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

        $this->assertInstanceOf(Application::class, $application);
        $this->assertInstanceOf(ParallelCommand::class, $application->find('run'));
        $this->assertInstanceOf(CoverageCommand::class, $application->find('coverage'));
    }
}
