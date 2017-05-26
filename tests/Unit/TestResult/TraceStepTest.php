<?php
declare(strict_types=1);

namespace Tests\Unit\TestResult;

use Paraunit\TestResult\TraceStep;
use Tests\BaseUnitTestCase;

/**
 * Class TraceStepTest
 * @package Unit\TestResult
 */
class TraceStepTest extends BaseUnitTestCase
{
    public function testStringCast()
    {
        $step = new TraceStep('func', 10);

        $this->assertEquals('func:10', (string) $step);
    }
}
