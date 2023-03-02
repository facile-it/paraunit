<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Logs\TestHook\Deprecation;
use Paraunit\Logs\TestHook\Error;
use Paraunit\Logs\TestHook\ExecutionStarted;
use Paraunit\Logs\TestHook\Failure;
use Paraunit\Logs\TestHook\Incomplete;
use Paraunit\Logs\TestHook\Passed;
use Paraunit\Logs\TestHook\PhpDeprecation;
use Paraunit\Logs\TestHook\PhpUnitDeprecation;
use Paraunit\Logs\TestHook\PhpUnitWarning;
use Paraunit\Logs\TestHook\PhpWarning;
use Paraunit\Logs\TestHook\Risky;
use Paraunit\Logs\TestHook\Skipped;
use Paraunit\Logs\TestHook\TestFinished;
use Paraunit\Logs\TestHook\TestPrepared;
use Paraunit\Logs\TestHook\TestRunnerWarning;
use Paraunit\Logs\TestHook\TestWarning;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class ParaunitExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        if (false === getenv(EnvVariables::PROCESS_UNIQUE_ID)) {
            return;
        }

        $facade->registerSubscribers(
            new TestPrepared(),
            new TestFinished(),
            new Error(),
            new Failure(),
            new ExecutionStarted(),
            new Incomplete(),
            new Risky(),
            new Deprecation(),
            new PhpDeprecation(),
            new PhpUnitDeprecation(),
            new Skipped(),
            new Passed(),
            new PhpWarning(),
            new PhpUnitWarning(),
            new TestWarning(),
            new TestRunnerWarning(),
        );
    }
}
