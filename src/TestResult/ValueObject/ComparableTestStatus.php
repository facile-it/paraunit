<?php

declare(strict_types=1);

namespace Paraunit\TestResult\ValueObject;

use PHPUnit\Framework\TestStatus\TestStatus;

interface ComparableTestStatus
{
    public function isMoreImportantThan(?self $status): bool;

    public function toPHPUnit(): TestStatus;
}
