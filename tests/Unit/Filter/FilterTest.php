<?php

declare(strict_types=1);

namespace Tests\Unit\Filter;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Filter\Filter;
use SebastianBergmann\FileIterator\Facade;
use Tests\BaseUnitTestCase;

class FilterTest extends BaseUnitTestCase
{
    /** @var string|null */
    private $absoluteConfigBaseDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->absoluteConfigBaseDir = $this->absoluteConfigBaseDir ?? \dirname(__DIR__, 2) . '/Stub/StubbedXMLConfigs' . DIRECTORY_SEPARATOR;
    }

    public function testFilterTestFilesGetsOnlyRequestedTestsuite(): void
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $filter = new Filter(new Facade(), $configFilePhpUnit, 'test_only_requested_testsuite');
        $result = $filter->filterTestFiles();

        $this->assertCount(1, $result);
        $this->assertEquals([$this->absoluteConfigBaseDir . '../FatalErrorTestStub.php'], $result);
    }

    public function testFilterTestFilesSupportsSuffixAttribute(): void
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_suffix_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $filter = new Filter(new Facade(), $configFilePhpUnit);
        $result = $filter->filterTestFiles();

        $this->assertEquals([realpath($this->absoluteConfigBaseDir . '../SegFaultTestStub.php')], $result);
    }

    public function testFilterTestFilesSupportsExcludeNodes(): void
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_node_exclude.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $filter = new Filter(new Facade(), $configFilePhpUnit, 'selected');
        $result = $filter->filterTestFiles();

        $this->assertEmpty($result);
    }

    public function testFilterTestFilesAvoidsDuplicateRuns(): void
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $filter = new Filter(new Facade(), $configFilePhpUnit);
        $result = $filter->filterTestFiles();

        $this->assertCount(count(array_unique($result)), $result);
    }

    public function testFilterTestFilesSupportsFileNodes(): void
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_node_file.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $filter = new Filter(new Facade(), $configFilePhpUnit, 'test_only_requested_testsuite');
        $result = $filter->filterTestFiles();

        $this->assertContains($this->absoluteConfigBaseDir . '../FatalErrorTestStub.php', $result);
        $this->assertNotContains($this->absoluteConfigBaseDir . '../SegFaultTestStub.php', $result);
    }

    public function testFilterTestFilesSupportsCaseInsensitiveStringFiltering(): void
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $filter = new Filter(new Facade(), $configFilePhpUnit, null, 'retry');
        $result = $filter->filterTestFiles();

        $this->assertContains($this->absoluteConfigBaseDir . '../PassThenRetryTestStub.php', $result);
        $this->assertNotContains($this->absoluteConfigBaseDir . '../FatalErrorTestStub.php', $result);
    }

    private function mockPHPUnitConfig(string $configFile): PHPUnitConfig
    {
        $this->assertFileExists($configFile, 'Mock not possible, config file to pass does not exist');

        $config = $this->prophesize(PHPUnitConfig::class);
        $config->getFileFullPath()
            ->willReturn($configFile);
        $config->getBaseDirectory()
            ->willReturn(dirname($configFile));

        return $config->reveal();
    }
}
