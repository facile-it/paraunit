<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TextSummary
 * @package Paraunit\Proxy\Coverage
 */
class TextSummary extends AbstractText
{
    /**
     * TextSummary constructor.
     * @param OutputInterface $output
     * @param bool $showColors
     * @param OutputFile|null $targetFile
     */
    public function __construct(OutputInterface $output, bool $showColors, OutputFile $targetFile = null)
    {
        parent::__construct($output, $showColors, true, $targetFile);
    }

    public static function getConsoleOptionName(): string
    {
        return 'text-summary';
    }
}
