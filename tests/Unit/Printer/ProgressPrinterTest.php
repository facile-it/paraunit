<?php

declare(strict_types=1);

namespace Tests\Unit\Printer;

use Paraunit\Lifecycle\TestCompleted;
use Paraunit\Logs\ValueObject\Test;
use Paraunit\Printer\ProgressPrinter;
use Paraunit\TestResult\ValueObject\TestOutcome;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Symfony\Component\Console\Output\Output;
use Tests\BaseUnitTestCase;
use Tests\Stub\UnformattedOutputStub;

class ProgressPrinterTest extends BaseUnitTestCase
{
    public function testOnTestCompletedOutcomeIsPrinted(): void
    {
        $output = new UnformattedOutputStub();

        $printer = new ProgressPrinter($output);

        $printer->onTestCompleted(new TestCompleted(new Test('Foo'), TestOutcome::Passed));

        $this->assertEquals('<ok>.</ok>', $output->getOutput());
    }

    public function testOnEngineEnd(): void
    {
        $output = new UnformattedOutputStub();
        $printer = new ProgressPrinter($output);

        $printer->onEngineEnd();

        $consoleOutput = $output->getOutput();
        $this->assertEquals(ProgressPrinter::MAX_CHAR_LENGTH + 1, strlen($consoleOutput));
        $this->assertStringEndsWith(" 0\n", $consoleOutput);
    }

    #[DataProvider('newLineTimesProvider')]
    public function testOnTestCompletedAddsCounterAndNewlineAtFullRow(int $times, int $newLineTimes): void
    {
        $output = $this->prophesize(Output::class);
        $output->write(Argument::any())
            ->shouldBeCalledTimes($times);
        $output->writeln(Argument::that(function ($input): bool {
            $this->assertEquals(6, strlen($input), 'Wrong output: ' . $input);

            return true;
        }))
            ->willReturn()
            ->shouldBeCalledTimes($newLineTimes);

        $printer = new ProgressPrinter($output->reveal());

        for ($i = 0; $i < $times; ++$i) {
            $printer->onTestCompleted(new TestCompleted(new Test('Foo'), TestOutcome::Passed));
        }
    }

    /**
     * @return int[][]
     */
    public static function newLineTimesProvider(): array
    {
        return [
            [73, 0],
            [74, 0],
            [75, 1],
            [195, 2],
            [222, 2],
            [223, 3],
        ];
    }

    public function testOnTestCompletedAndOnEngineEndWorkWellTogether(): void
    {
        $output = new UnformattedOutputStub();
        $printer = new ProgressPrinter($output);

        for ($i = 0; $i < 100; ++$i) {
            $printer->onTestCompleted(new TestCompleted(new Test('Foo'), TestOutcome::Passed));
        }
        $printer->onEngineEnd();

        $expectedOutput = str_repeat('<ok>.</ok>', 74) . '    74' . "\n"
            . str_repeat('<ok>.</ok>', 26)
            . str_repeat(' ', ProgressPrinter::MAX_CHAR_LENGTH - (26 + 3))
            . '100' . "\n";
        $this->assertSame($expectedOutput, $output->getOutput());
    }
}
