<?php

namespace Tests;

use Paraunit\Configuration\ParallelCoverageConfiguration;

/**
 * Class BaseFunctionalWithCoverageTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalWithCoverageTestCase extends BaseFunctionalTestCase
{
    protected function loadContainer()
    {
        $configuration = new ParallelCoverageConfiguration();

        $this->container = $configuration->buildContainer();
    }
}
