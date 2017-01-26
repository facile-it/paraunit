<?php

namespace Paraunit\Bin;

use Paraunit\Command\CoverageCommand;
use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\CoverageConfiguration;
use Symfony\Component\Console\Application;

/**
 * Class Paraunit
 * @package Paraunit\Bin
 */
class Paraunit
{
    const VERSION = '0.8.2';

    public static function createApplication()
    {
        $application = new Application('Paraunit', self::VERSION);

        $parallelCommand = new ParallelCommand(new ParallelConfiguration());
        $application->add($parallelCommand);

        $CoverageCommand = new CoverageCommand(new CoverageConfiguration());
        $application->add($CoverageCommand);

        return $application;
    }
}
