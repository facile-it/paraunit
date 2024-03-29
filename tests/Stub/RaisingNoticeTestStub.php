<?php

declare(strict_types=1);

namespace Tests\Stub;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RaisingNoticeTestStub extends TestCase
{
    #[DataProvider('errorProvider')]
    public function testRaise(string $errorMessage, int $errorLevel): never
    {
        trigger_error($errorMessage, $errorLevel);
        $this->fail();
    }

    public static function errorProvider(): array
    {
        return [
            ['YOU SHOULD NOT SEE THIS -- E_USER_NOTICE', E_USER_NOTICE],
            ['YOU SHOULD NOT SEE THIS -- E_USER_WARNING', E_USER_WARNING],
            ['YOU SHOULD NOT SEE THIS -- E_USER_ERROR', E_USER_ERROR],
        ];
    }

    public function testVarDump(): never
    {
        var_dump('YOU SHOULD NOT SEE THIS -- var_dump');
        $this->fail();
    }
}
