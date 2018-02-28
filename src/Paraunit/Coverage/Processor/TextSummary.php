<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;

/**
 * Class TextSummary
 * @package Paraunit\Proxy\Coverage
 */
class TextSummary extends AbstractText
{
    /**
     * TextSummary constructor.
     * @param OutputFile $targetFile
     * @param bool $showColors
     */
    public function __construct(OutputFile $targetFile, bool $showColors)
    {
        parent::__construct($targetFile, $showColors, true);
    }

    public static function getConsoleOptionName(): string
    {
        return 'text-summary';
    }
}
