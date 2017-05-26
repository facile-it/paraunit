<?php
declare(strict_types=1);

namespace Tests\Unit\Process;

use Paraunit\Process\SymfonyProcessWrapper;
use Tests\BaseUnitTestCase;

/**
 * Class SymfonyProcessWrapperTest
 * @package Unit\Process
 */
class SymfonyProcessWrapperTest extends BaseUnitTestCase
{
    public function testAddTestResultShouldResetExpectingFlag()
    {
        $process = new SymfonyProcessWrapper('/some/path/SomeTest.php', 'commandline', 'uuid');
        $process->setWaitingForTestResult(true);
        $this->assertTrue($process->isWaitingForTestResult());

        $process->addTestResult($this->mockPrintableTestResult());

        $this->assertFalse($process->isWaitingForTestResult());
    }

    /**
     * @dataProvider commandlineProvider
     */
    public function testGetFilenameRegressionWithCoverageCommandline(string $commandline)
    {
        $process = new SymfonyProcessWrapper('/some/path/FilesRecapPrinterTest.php', $commandline, 'uniqueId');

        $this->assertEquals('FilesRecapPrinterTest.php', $process->getFilename());
    }

    public function commandlineProvider(): array
    {
        return [
            ['/home/user/workspace/paraunit/vendor/phpunit/phpunit/phpunit -c /home/user/workspace/paraunit/phpunit.xml.dist --log-json /dev/shm/paraunit/20161112-00544058265a40b2820/logs/680aa6f15bc1392db3b8f933ff2c2d35.json.log --group=none --coverage-php /dev/shm/paraunit/20161112-00544058265a40b2820/coverage/680aa6f15bc1392db3b8f933ff2c2d35.php /home/user/workspace/paraunit/tests/Functional/Printer/FilesRecapPrinterTest.php'],
            ['/home/user/workspace/paraunit/vendor/phpunit/phpunit/phpunit -c /home/user/workspace/paraunit/phpunit.xml.dist --log-json /dev/shm/paraunit/20161112-01060158265ce9c97e3/logs/7d3773479b8d9b9068242acacebb03ac.json.log --group=none --coverage-php /dev/shm/paraunit/20161112-01060158265ce9c97e3/coverage/7d3773479b8d9b9068242acacebb03ac.php /home/user/workspace/paraunit/tests/Functional/Printer/FilesRecapPrinterTest.php'],
        ];
    }

    /**
     * @param string $testFileName
     * @dataProvider fileNameProvider
     */
    public function testGetFilenameRegressionWithNumbers(string $testFileName, string $expectedFilename)
    {
        $commandline = implode(' ', [
            '/home/user/workspace/paraunit/vendor/phpunit/phpunit/phpunit',
            '-c /home/user/workspace/paraunit/phpunit.xml.dist',
            '--log-json /dev/shm/paraunit/20161112-00544058265a40b2820/logs/680aa6f15bc1392db3b8f933ff2c2d35.json.log',
            $testFileName,
        ]);

        $process = new SymfonyProcessWrapper($testFileName, $commandline, 'uniqueId');

        $this->assertSame($expectedFilename, $process->getFilename());
    }

    public function fileNameProvider(): array
    {
        return [
            ['/home/user/workspace/paraunit/tests/FileTest.php', 'FileTest.php'],
            ['/home/user/workspace/paraunit/tests/SomeFile.php', 'SomeFile.php'],
            ['/home/user/workspace/paraunit/tests/Some2017File.php', 'Some2017File.php'],
            ['C:\Tests\SomeFile.php', 'SomeFile.php'],
            ['D:\Tests\SomeFile.php', 'SomeFile.php'],
            ['D:\Tests\Some_File.php', 'Some_File.php'],
            ['D:\Tests\Some-File.php', 'Some-File.php'],
            ['C:\Some2017File.php', 'Some2017File.php'],
        ];
    }
}
