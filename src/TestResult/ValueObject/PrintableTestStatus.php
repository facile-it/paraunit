<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

interface PrintableTestStatus
{
    public function getTitle(): string;

    public function getSymbol(): string;
}
