<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

use Paraunit\Logs\TestHook\BeforeTest;
use Paraunit\Logs\TestHook\Deprecation;
use Paraunit\Logs\TestHook\Error;
use Paraunit\Logs\TestHook\ExecutionStarted;
use Paraunit\Logs\TestHook\Failure;
use Paraunit\Logs\TestHook\Incomplete;
use Paraunit\Logs\TestHook\Passed;
use Paraunit\Logs\TestHook\Risky;
use Paraunit\Logs\TestHook\Skipped;
use Paraunit\Logs\TestHook\Warning;
use PHPUnit\Event\Facade;

// TODO - wait for feedback and refactor accordingly
// see https://github.com/sebastianbergmann/phpunit/issues/4807
// or  https://github.com/sebastianbergmann/phpunit/issues/4676
Facade::registerSubscriber(new BeforeTest());
Facade::registerSubscriber(new Error());
Facade::registerSubscriber(new Failure());
Facade::registerSubscriber(new ExecutionStarted());
Facade::registerSubscriber(new Incomplete());
Facade::registerSubscriber(new Risky());
Facade::registerSubscriber(new Deprecation());
Facade::registerSubscriber(new Skipped());
Facade::registerSubscriber(new Passed());
Facade::registerSubscriber(new Warning());
