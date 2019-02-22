<?php

declare(strict_types=1);

namespace Tests\Unit\Bin;

use Paraunit\Bin\Paraunit;
use Paraunit\Command\CoverageCommand;
use Paraunit\Command\ParallelCommand;
use Tests\BaseUnitTestCase;

class ParaunitTest extends BaseUnitTestCase
{
    public function testCreateApplication(): void
    {
        $application = Paraunit::createApplication();

        $this->assertInstanceOf(ParallelCommand::class, $application->find('run'));
        $this->assertInstanceOf(CoverageCommand::class, $application->find('coverage'));
    }
}
