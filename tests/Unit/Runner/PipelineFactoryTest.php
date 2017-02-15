<?php

namespace Tests\Unit\Runner;

use Paraunit\Runner\Pipeline;
use Paraunit\Runner\PipelineFactory;
use Tests\BaseUnitTestCase;

/**
 * Class PipelineFactoryTest
 * @package Tests\Unit\Runner
 */
class PipelineFactoryTest extends BaseUnitTestCase
{
    public function testCreate()
    {
        $dispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface')->reveal();
        $factory = new PipelineFactory($dispatcher);

        $pipeline = $factory->create(5);

        $this->assertInstanceOf('Paraunit\Runner\Pipeline', $pipeline);
        $this->assertEquals(5, $pipeline->getNumber());
    }
}
