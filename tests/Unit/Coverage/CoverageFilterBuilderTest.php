<?php

namespace Tests\Unit\Coverage;

use Paraunit\Coverage\CoverageFilterBuilder;
use Tests\BaseUnitTestCase;

/**
 * Class CoverageFilterBuilderTest
 * @package Tests\Unit\Coverage
 */
class CoverageFilterBuilderTest extends BaseUnitTestCase
{
    public function testCreateFromConfiguration()
    {
        $this->markTestIncomplete();

        $filter = $this->prophesize('\PHP_CodeCoverage_Filter');
        $filter->addDirectoryToWhitelist();
        $builder = new CoverageFilterBuilder();

//        $result = $builder->createFromConfiguration($configFile);

//        $this->assertInstanceOf(, $result);
    }
}
