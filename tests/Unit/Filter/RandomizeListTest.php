<?php

declare(strict_types=1);

namespace Tests\Unit\Filter;

use Paraunit\Filter\RandomizeList;
use Paraunit\Filter\TestList;
use Tests\BaseUnitTestCase;

class RandomizeListTest extends BaseUnitTestCase
{
    public function testGetList(): void
    {
        $baseList = $this->prophesize(TestList::class);
        $baseList->getTests()
            ->willReturn($this->mockTestList());

        $randomizeList = new RandomizeList($baseList->reveal());

        $this->assertNotEquals($randomizeList->getTests(), $randomizeList->getTests(), 'Got the same order twice, it is not random!');
    }

    /**
     * @return string[]
     */
    private function mockTestList(): array
    {
        $list = [];

        foreach (range(1, 100) as $i) {
            $list[] = 'some/stub/test' . $i . '.php';
        }

        return $list;
    }
}
