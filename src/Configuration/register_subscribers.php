<?php

declare(strict_types=1);

use Paraunit\Parser\JSON\TestHook;
use PHPUnit\Event\Facade;

Facade::registerSubscriber(new TestHook\BeforeTest());
Facade::registerSubscriber(new TestHook\Error());
Facade::registerSubscriber(new TestHook\Failure());
Facade::registerSubscriber(new TestHook\Incomplete());
Facade::registerSubscriber(new TestHook\Risky());
Facade::registerSubscriber(new TestHook\Skipped());
Facade::registerSubscriber(new TestHook\Successful());
Facade::registerSubscriber(new TestHook\Warning());
