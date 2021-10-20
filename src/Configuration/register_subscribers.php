<?php

declare(strict_types=1);

\PHPUnit\Event\Facade::registerSubscriber(new \Paraunit\Parser\JSON\TestHook\BeforeTest());
\PHPUnit\Event\Facade::registerSubscriber(new \Paraunit\Parser\JSON\TestHook\Successful());
