<?php

namespace Paraunit\Bin;

use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\ParallelConfiguration;
use Paraunit\Configuration\ParallelCoverageConfiguration;
use Symfony\Component\Console\Application;

/**
 * Class Paraunit
 * @package Paraunit\Bin
 */
class Paraunit
{
    const VERSION = '0.7';

    public static function bootstrap()
    {
        $application = new Application('Paraunit', self::VERSION);

        $parallelCommand = new ParallelCommand(new ParallelConfiguration());
        $application->add($parallelCommand);

        $CoverageCommand = new ParallelCommand(new ParallelCoverageConfiguration());
        $application->add($CoverageCommand);

        $application->run();
    }
}
