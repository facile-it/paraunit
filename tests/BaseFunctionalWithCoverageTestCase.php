<?php

namespace Tests;

use Paraunit\Configuration\CoverageConfiguration;

/**
 * Class BaseFunctionalWithCoverageTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalWithCoverageTestCase extends BaseFunctionalTestCase
{
    protected function loadContainer()
    {
        $configuration = new CoverageConfiguration();

        $this->container = $configuration->buildContainer();
    }
}
