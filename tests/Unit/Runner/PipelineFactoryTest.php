<?php

namespace Tests\Unit\Runner;

use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineFactory;
use Tests\BaseUnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PipelineFactoryTest
 * @package Tests\Unit\Runner
 */
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
