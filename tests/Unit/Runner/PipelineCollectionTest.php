<?php

namespace Tests\Unit\Runner;

use Paraunit\Runner\PipelineCollection;
use Tests\BaseUnitTestCase;

/**
 * Class PipelineCollectionTest
 * @package Tests\Unit\Runner
 */
class PipelineCollectionTest extends BaseUnitTestCase
{
    public function testInstantiation()
    {
        $factory = $this->prophesize('Paraunit\Runner\PipelineFactory');
        for ($i = 0; $i < 5; $i++) {
            $factory->create($i)
                ->shouldBeCalledTimes(1)
                ->willReturn($this->prophesize('Paraunit\Runner\Pipeline')->reveal());
        }
        
        new PipelineCollection($factory->reveal(), 5);
    }
}
