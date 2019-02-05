<?php

declare(strict_types=1);

namespace Tests\Unit\Filter;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Filter\Filter;
use Paraunit\Proxy\PHPUnitUtilXMLProxy;
use PHPUnit\Util\Xml;
use SebastianBergmann\FileIterator\Facade;
use Tests\BaseUnitTestCase;

class FilterTest extends BaseUnitTestCase
{
    /** @var string */
    private $absoluteConfigBaseDir;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->absoluteConfigBaseDir = \dirname(__DIR__, 2) . '/Stub/StubbedXMLConfigs' . DIRECTORY_SEPARATOR;
    }

    public function testFilterTestFilesGetsOnlyRequestedTestsuite()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $testSuiteName = 'test_only_requested_testsuite';

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/OnlyTestSuiteTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', [])
            ->willReturn([$file1])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', [])
            ->willReturn([$file2])
            ->shouldNotBeCalled();

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit, $testSuiteName);

        $result = $filter->filterTestFiles();

        $this->assertCount(1, $result);
        $this->assertEquals([$file1], $result);
    }

    public function testFilterTestFilesSupportsSuffixAttribute()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_suffix_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/OnlyTestSuiteTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'TestSuffix.php', '', [])
            ->willReturn([$file1])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', [])
            ->willReturn([$file2])
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit);

        $result = $filter->filterTestFiles();
        $this->assertEquals([$file1, $file2], $result);
    }

    public function testFilterTestFilesSupportsPrefixAttribute()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_prefix_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/TestPrefixOneTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', 'TestPrefix', [])
            ->willReturn([$file1])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', [])
            ->willReturn([$file2])
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit);

        $result = $filter->filterTestFiles();
        $this->assertEquals([$file1, $file2], $result);
    }

    public function testFilterTestFilesSupportsExcludeNodes()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_node_exclude.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $excludeArray1 = [
            '/path/to/exclude1',
            '/path/to/exclude2',
        ];

        $excludeArray2 = [
            '/path/to/exclude3',
            '/path/to/exclude4',
        ];

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/TestPrefixOneTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', 'TestPrefix', $excludeArray1)
            ->willReturn([$file1])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', $excludeArray2)
            ->willReturn([$file2])
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit);

        $result = $filter->filterTestFiles();
        $this->assertEquals([$file1, $file2], $result);
    }

    public function testFilterTestFilesAvoidsDuplicateRuns()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file = $this->absoluteConfigBaseDir . './only/selected/test/suite/SameFile.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', [])
            ->willReturn([$file])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', [])
            ->willReturn([$file])
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit);

        $result = $filter->filterTestFiles();
        $this->assertCount(1, $result);
        $this->assertEquals([$file], $result);
    }

    public function testFilterTestFilesSupportsFileNodes()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_node_file.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/TestPrefixOneTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', [])
            ->willReturn([$file1])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', [])
            ->willReturn([$file2])
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit);

        $result = $filter->filterTestFiles();
        $this->assertEquals(
            [
                $file1,
                $this->absoluteConfigBaseDir . './this/file.php',
                $this->absoluteConfigBaseDir . './this/file2.php',
                $file2,
            ],
            $result
        );
    }

    public function testFilterTestFilesSupportsCaseInsensitiveStringFiltering()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(PHPUnitUtilXMLProxy::class);
        $utilXml->loadFile($configFile)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/ThisTest.php';
        $file2 = $this->absoluteConfigBaseDir . './only/selected/test/suite/ThisTooTest.php';
        $file3 = $this->absoluteConfigBaseDir . './only/selected/test/suite/NotHereTest.php';
        $file4 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(Facade::class);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', [])
            ->willReturn([$file1, $file2, $file3])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', [])
            ->willReturn([$file4])
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal(), $configFilePhpUnit, null, 'this');

        $result = $filter->filterTestFiles();

        $this->assertCount(2, $result);
        $this->assertEquals([$file1, $file2], $result);
    }

    /**
     * @param string $fileName
     *
     * @throws \Exception
     *
     * @return \DOMDocument
     */
    private function getStubbedXMLConf(string $fileName): \DOMDocument
    {
        $filePath = realpath($fileName);

        if (! $filePath || ! file_exists($filePath)) {
            throw new \RuntimeException('Stub XML config file missing: ' . $fileName);
        }

        return Xml::loadFile($filePath);
    }

    private function mockPHPUnitConfig(string $configFile)
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
