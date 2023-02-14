<?php

namespace Tests\Stub\CoverageOutput;

use Paraunit\Proxy\Coverage\FakeDriver;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Filter;
$codeCoverage = new CodeCoverage(new FakeDriver(), new Filter());
$codeCoverage->setTests(['foo' => ['size' => '123', 'status' => 'bar']]);
return $codeCoverage;
