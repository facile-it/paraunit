<?php

namespace Paraunit\Tests\Unit\Filter;

use Paraunit\Filter\Filter;
use Paraunit\Proxy\PHPUnit_Util_XML_Proxy;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterTestFiles_gets_only_requested_testsuite()
    {
        $configFile = 'stubbed_for_filter_test.xml';
        $testSuiteName = 'test_only_requested_testsuite';

        $utilXml = $this->prophesize(PHPUnit_Util_XML_Proxy::class);
        $utilXml->loadFile($configFile, false, true, true)
            ->willReturn($this->getStubbedXMLConf($configFile))
            ->shouldBeCalled();

        $fileIterator = $this->prophesize(\File_Iterator_Facade::class);
        $fileIterator->getFilesAsArray('./only/selected/test/suite/', 'Test.php', '', [])
            ->willReturn(['OnlyTestSuiteTest.php'])
            ->shouldBeCalledTimes(1);
        $fileIterator->getFilesAsArray('./wrong/test/suite/', 'Test.php', '', [])
            ->willReturn(['WrongTest.php'])
            ->shouldNotBeCalled();

        $filter = new Filter($utilXml->reveal(), $fileIterator->reveal());

        $result = $filter->filterTestFiles($configFile, $testSuiteName);

        $this->assertCount(1, $result);
        $this->assertEquals(['OnlyTestSuiteTest.php'], $result);
    }

    /**
     * @param string $fileName
     * @return string
     * @throws \Exception
     */
    private function getStubbedXMLConf($fileName)
    {
        $filePath = realpath(__DIR__. '/../../Stub/StubbedXMLConfigs/' . $fileName);

        if (!file_exists($filePath)) {
            throw new \Exception('Stub XML config file missing: '. $filePath);
        }

        return \PHPUnit_Util_XML::loadFile($filePath, false, true, true);
    }
}
