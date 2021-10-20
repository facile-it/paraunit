<?php

\PHPUnit\Event\Facade::registerSubscriber(new \Paraunit\Parser\JSON\TestHook\BeforeTest());
