<?php

namespace Tests\Unit\Filter;

use Paraunit\Filter\Filter;
use Tests\BaseUnitTestCase;

/**
 * Class FilterTest
 * @package Tests\Unit\Filter
 */
class FilterTest extends BaseUnitTestCase
{
    const PHPUNIT_UTIL_XML_PROXY_CLASS = 'Paraunit\Proxy\PHPUnitUtilXMLProxy';
    const FILE_ITERATOR_FACADE_CLASS = '\File_Iterator_Facade';

    /** @var  string */
    private $absoluteConfigBaseDir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->absoluteConfigBaseDir = realpath(__DIR__ . '/../../Stub/StubbedXMLConfigs/') . DIRECTORY_SEPARATOR;
    }

    public function testFilterTestFilesGetsOnlyRequestedTestsuite()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $testSuiteName = 'test_only_requested_testsuite';

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/OnlyTestSuiteTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file1))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file2))
            ->shouldNotBeCalled();

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit, $testSuiteName);

        $this->assertCount(1, $result);
        $this->assertEquals(array($file1), $result);
    }

    public function testFilterTestFilesSupportsSuffixAttribute()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_suffix_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/OnlyTestSuiteTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'TestSuffix.php', '', array())
            ->willReturn(array($file1))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file2))
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit);
        $this->assertEquals(array($file1, $file2), $result);
    }

    public function testFilterTestFilesSupportsPrefixAttribute()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_prefix_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/TestPrefixOneTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', 'TestPrefix', array())
            ->willReturn(array($file1))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file2))
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit);
        $this->assertEquals(array($file1, $file2), $result);
    }

    public function testFilterTestFilesSupportsExcludeNodes()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_node_exclude.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $excludeArray1 = array(
            '/path/to/exclude1',
            '/path/to/exclude2',
        );

        $excludeArray2 = array(
            '/path/to/exclude3',
            '/path/to/exclude4',
        );

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/TestPrefixOneTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', 'TestPrefix', $excludeArray1)
            ->willReturn(array($file1))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', $excludeArray2)
            ->willReturn(array($file2))
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit);
        $this->assertEquals(array($file1, $file2), $result);
    }

    public function testFilterTestFilesAvoidsDuplicateRuns()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file = $this->absoluteConfigBaseDir . './only/selected/test/suite/SameFile.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file))
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit);
        $this->assertCount(1, $result);
        $this->assertEquals(array($file), $result);
    }

    public function testFilterTestFilesSupportsFileNodes()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_node_file.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/TestPrefixOneTest.php';
        $file2 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file1))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file2))
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit);
        $this->assertEquals(
            array(
                $file1,
                $this->absoluteConfigBaseDir . './this/file.php',
                $this->absoluteConfigBaseDir . './this/file2.php',
                $file2,
            ),
            $result
        );
    }

    public function testFilterTestFilesSupportsCaseInsensitiveStringFiltering()
    {
        $configFile = $this->absoluteConfigBaseDir . 'stubbed_for_filter_test.xml';
        $configFilePhpUnit = $this->mockPHPUnitConfig($configFile);

        $utilXml = $this->prophesize(static::PHPUNIT_UTIL_XML_PROXY_CLASS);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $file1 = $this->absoluteConfigBaseDir . './only/selected/test/suite/ThisTest.php';
        $file2 = $this->absoluteConfigBaseDir . './only/selected/test/suite/ThisTooTest.php';
        $file3 = $this->absoluteConfigBaseDir . './only/selected/test/suite/NotHereTest.php';
        $file4 = $this->absoluteConfigBaseDir . './other/test/suite/OtherTest.php';

        $fileIterator = $this->prophesize(static::FILE_ITERATOR_FACADE_CLASS);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './only/selected/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file1, $file2, $file3))
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray($this->absoluteConfigBaseDir . './other/test/suite/', 'Test.php', '', array())
            ->willReturn(array($file4))
            ->shouldBeCalledTimes(1);

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFilePhpUnit, null, 'this');

        $this->assertCount(2, $result);
        $this->assertEquals(array($file1, $file2), $result);
    }

    /**
     * @param string $fileName
     *
     * @return string
     *
     * @throws \Exception
     */
    private function getStubbedXMLConf($fileName)
    {
        $filePath = realpath($fileName);

        if (! file_exists($filePath)) {
            throw new \RuntimeException('Stub XML config file missing: ' . $fileName);
        }

        return \PHPUnit_Util_XML::loadFile($filePath, false, true, true);
    }

    private function mockPHPUnitConfig($configFile)
    {
        $this->assertFileExists($configFile, 'Mock not possible, config file to pass does not exist');

        $config = $this->prophesize('Paraunit\Configuration\PHPUnitConfig');
        $config->getFileFullPath()
            ->willReturn($configFile);
        $config->getBaseDirectory()
            ->willReturn(dirname($configFile));

        return $config->reveal();
    }
}
