<?php

declare(strict_types=1);

namespace Tests;

/**
 * Class BaseFunctionalTestCase
 * @package Paraunit\Tests
 */
abstract class BaseFunctionalTestCase extends BaseIntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->loadContainer();
    }
}
