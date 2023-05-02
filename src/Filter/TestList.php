<?php

declare(strict_types=1);

namespace Paraunit\Filter;

interface TestList
{
    /**
     * @return string[] a list of test files to be executed
     */
    public function getTests(): array;
}
