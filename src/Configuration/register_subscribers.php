<?php

declare(strict_types=1);

use Paraunit\Parser\TestHook;
use PHPUnit\Event\Facade;

// TODO - wait for feedback and refactor accordingly
// see https://github.com/sebastianbergmann/phpunit/issues/4807
// or  https://github.com/sebastianbergmann/phpunit/issues/4676

Facade::registerSubscriber(new TestHook\BeforeTest());
Facade::registerSubscriber(new TestHook\Error());
Facade::registerSubscriber(new TestHook\Failure());
Facade::registerSubscriber(new TestHook\Incomplete());
Facade::registerSubscriber(new TestHook\Risky());
Facade::registerSubscriber(new TestHook\Skipped());
Facade::registerSubscriber(new TestHook\Successful());
Facade::registerSubscriber(new TestHook\Warning());
