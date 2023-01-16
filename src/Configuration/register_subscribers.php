<?php

declare(strict_types=1);
use Paraunit\Parser\TestHook\BeforeTest;
use Paraunit\Parser\TestHook\Error;
use Paraunit\Parser\TestHook\Failure;
use Paraunit\Parser\TestHook\Incomplete;
use Paraunit\Parser\TestHook\Passed;
use Paraunit\Parser\TestHook\Risky;
use Paraunit\Parser\TestHook\Skipped;
use Paraunit\Parser\TestHook\Warning;
use PHPUnit\Event\Facade;

// TODO - wait for feedback and refactor accordingly
// see https://github.com/sebastianbergmann/phpunit/issues/4807
// or  https://github.com/sebastianbergmann/phpunit/issues/4676

Facade::registerSubscriber(new BeforeTest());
Facade::registerSubscriber(new Error());
Facade::registerSubscriber(new Failure());
Facade::registerSubscriber(new Incomplete());
Facade::registerSubscriber(new Risky());
Facade::registerSubscriber(new Skipped());
Facade::registerSubscriber(new Passed());
Facade::registerSubscriber(new Warning());
