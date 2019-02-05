<?php

declare(strict_types=1);

namespace Tests;

abstract class BaseFunctionalTestCase extends BaseIntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->loadContainer();
    }
}
