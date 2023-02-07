<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Logs\TestHook\Deprecation;
use Paraunit\Logs\TestHook\Error;
use Paraunit\Logs\TestHook\ExecutionStarted;
use Paraunit\Logs\TestHook\Failure;
use Paraunit\Logs\TestHook\Incomplete;
use Paraunit\Logs\TestHook\Passed;
use Paraunit\Logs\TestHook\PhpUnitWarning;
use Paraunit\Logs\TestHook\PhpWarning;
use Paraunit\Logs\TestHook\Risky;
use Paraunit\Logs\TestHook\Skipped;
use Paraunit\Logs\TestHook\TestFinished;
use Paraunit\Logs\TestHook\TestPrepared;
use Paraunit\Logs\TestHook\TestRunnerWarning;
use Paraunit\Logs\TestHook\TestWarning;
use PHPUnit\Event\Facade;

// TODO - use extensions - https://github.com/facile-it/paraunit/issues/179
Facade::registerSubscriber(new TestPrepared());
Facade::registerSubscriber(new TestFinished());
Facade::registerSubscriber(new Error());
Facade::registerSubscriber(new Failure());
Facade::registerSubscriber(new ExecutionStarted());
Facade::registerSubscriber(new Incomplete());
Facade::registerSubscriber(new Risky());
Facade::registerSubscriber(new Deprecation());
Facade::registerSubscriber(new Skipped());
Facade::registerSubscriber(new Passed());
Facade::registerSubscriber(new PhpWarning());
Facade::registerSubscriber(new PhpUnitWarning());
Facade::registerSubscriber(new TestWarning());
Facade::registerSubscriber(new TestRunnerWarning());
