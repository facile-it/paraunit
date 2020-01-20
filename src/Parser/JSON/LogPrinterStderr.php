<?php

declare(strict_types=1);

namespace Paraunit\Parser\JSON;

/**
 * Class LogPrinterStderr
 */
class LogPrinterStderr extends LogPrinter
{
    public function __construct()
    {
        parent::__construct('php://stderr');
    }
}
