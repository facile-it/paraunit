<?php

declare(strict_types=1);

namespace Tests\Unit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\Processor\Text;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseUnitTestCase;

class TextTest extends BaseUnitTestCase
{
    #[DataProvider('colorProvider')]
    public function testWriteToFile(bool $withColors, string $expectedString): void
    {
        $targetFile = new OutputFile(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coverage.txt');
        $text = new Text(
            $this->prophesize(OutputInterface::class)->reveal(),
            $withColors,
            $targetFile
        );

        $this->assertFileDoesNotExist($targetFile->getFilePath());

        $text->process($this->createCodeCoverage());

        $this->assertFileExists($targetFile->getFilePath());
        $content = file_get_contents($targetFile->getFilePath());
        unlink($targetFile->getFilePath());
        $this->assertNotFalse($content);
        $this->assertStringContainsString($expectedString, $content);
    }

    #[DataProvider('colorProvider')]
    public function testWriteToOutput(bool $withColors, string $expectedString): void
    {
        $output = $this->prophesize(OutputInterface::class);
        $output->writeln(Argument::containingString($expectedString))
            ->shouldBeCalledTimes(1);
        $text = new Text($output->reveal(), $withColors);

        $text->process($this->createCodeCoverage());
    }

    /**
     * @return (bool|string)[][]
     */
    public static function colorProvider(): array
    {
        return [
            [false, 'Code Coverage Report:'],
            [true, "\x1b[1;37;40mCode Coverage Report:"],
        ];
    }
}
