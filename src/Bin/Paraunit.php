<?php

declare(strict_types=1);

namespace Paraunit\Bin;

use Jean85\PrettyVersions;
use Paraunit\Command\CoverageCommand;
use Paraunit\Command\ParallelCommand;
use Paraunit\Configuration\CoverageConfiguration;
use Paraunit\Configuration\ParallelConfiguration;
use Symfony\Component\Console\Application;

class Paraunit
{
    public static function createApplication(): Application
    {
        $application = new Application('Paraunit', self::getVersion());

        $parallelCommand = new ParallelCommand(new ParallelConfiguration());
        $coverageCommand = new CoverageCommand(new CoverageConfiguration());
        $application->addCommands([$parallelCommand, $coverageCommand]);

        return $application;
    }

    public static function getVersion(): string
    {
        return PrettyVersions::getVersion('facile-it/paraunit')->getPrettyVersion();
    }

    public static function getPHPUnitVersion(): string
    {
        return PrettyVersions::getVersion('phpunit/phpunit')->getPrettyVersion();
    }
}
