<?php

declare(strict_types=1);

namespace Paraunit\Coverage;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Facade;

class PhpUnitFacadeFactory
{
    public function create(CodeCoverage $codeCoverage): Facade
    {
        return Facade::fromObject($codeCoverage);
    }
}
