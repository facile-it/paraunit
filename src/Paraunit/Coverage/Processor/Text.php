<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Proxy\Coverage\CodeCoverage;

/**
 * Class Text
 * @package Paraunit\Proxy\Coverage
 */
class Text extends AbstractText
{
    /**
     * Text constructor.
     * @param OutputFile $targetFile
     * @param bool $showColors
     */
    public function __construct(OutputFile $targetFile, bool $showColors)
    {
        parent::__construct($targetFile, $showColors, false);
    }

     public static function getConsoleOptionName(): string
    {
        return 'text';
    }
}
