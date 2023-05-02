<?php

declare(strict_types=1);

namespace Paraunit\Filter;

class RandomizeList implements TestList
{
    public function __construct(private readonly TestList $testList)
    {
    }

    public function getTests(): array
    {
        $tests = $this->testList->getTests();

        shuffle($tests);

        return $tests;
    }
}
