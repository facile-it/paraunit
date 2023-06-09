<?php

declare(strict_types=1);

namespace Paraunit\Util\Log\Helpers;

class DOMDocumentAttributes
{
    public const BASE_SUITE_ATTRIBUTES = [
        'name' => 'All Suites',
        'tests' => '0',
        'assertions' => '0',
        'errors' => '0',
        'failures' => '0',
        'skipped' => '0',
        'time' => '0',
     ];

    public function getBaseSuiteAttributes(): array
    {
        return self::BASE_SUITE_ATTRIBUTES;
    }
}