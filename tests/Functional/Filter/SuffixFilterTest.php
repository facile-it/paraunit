<?php

declare(strict_types=1);

namespace Tests\Functional\Filter;

use Paraunit\Filter\Filter;
use Tests\BaseFunctionalTestCase;

class SuffixFilterTest extends BaseFunctionalTestCase
{
    protected function setup(): void
    {
        $this->setOption('configuration', $this->getStubPath() . DIRECTORY_SEPARATOR . 'phpunit_with_2_testsuites.xml');
        $this->setOption('test-suffix', 'GreenTestStub.php');

        parent::setup();
    }

    public function testFilterTestBySuffixFiles(): void
    {
        /** @var Filter $filter */
        $filter = $this->getService(Filter::class);

        $files = $filter->filterTestFiles();

        $this->assertCount(1, $files);

        $fileExploded = explode('/', $files[0]);

        $this->assertSame('ThreeGreenTestStub.php', end($fileExploded));
    }
}
