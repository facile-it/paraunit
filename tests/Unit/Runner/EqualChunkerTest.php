<?php

declare(strict_types=1);

namespace Tests\Unit\Runner;

use Paraunit\Configuration\ChunkSize;
use Paraunit\Runner\EqualChunker;
use Tests\BaseUnitTestCase;

class EqualChunkerTest extends BaseUnitTestCase
{
    /**
     * @dataProvider provideChunk
     */
    public function testChunk(array $tests, int $chunkSize, array $expected): void
    {
        $chunker = new EqualChunker();
        self::assertSame($expected, $chunker->chunk($tests, new ChunkSize($chunkSize)));
    }

    public function provideChunk(): iterable
    {
        $tests = [
            'Test1.php',
            'Test2.php',
            'Test3.php',
        ];
        yield 'chunkSize = 1' => [
            $tests,
            1,
            [
                [$tests[0]],
                [$tests[1]],
                [$tests[2]],
            ]
        ];
        yield 'chunkSize = 2' => [
            $tests,
            2,
            [
                [$tests[0], $tests[1]],
                [$tests[2]],
            ]
        ];
        yield 'chunkSize = 3' => [
            $tests,
            3,
            [$tests]
        ];
        yield 'chunkSize = bigger than n' => [
            $tests,
            83,
            [$tests]
        ];
    }
}
