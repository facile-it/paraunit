<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\BaseUnitTestCase;

class PipelineFactoryTest extends BaseUnitTestCase
{
    public function testCreate()
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class)->reveal();
        $factory = new PipelineFactory($dispatcher);

        $pipeline = $factory->create(5);

        $this->assertInstanceOf(Pipeline::class, $pipeline);
        $this->assertEquals(5, $pipeline->getNumber());
    }
}
