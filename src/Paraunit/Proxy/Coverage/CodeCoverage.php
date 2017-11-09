<?php

declare(strict_types=1);

namespace Paraunit\Proxy\Coverage;

/**
 * Class CodeCoverage
 * @package Paraunit\Proxy\Coverage
 */
class CodeCoverage extends \SebastianBergmann\CodeCoverage\CodeCoverage
{
    public function __construct()
    {
        parent::__construct(new FakeDriver());
    }
}
