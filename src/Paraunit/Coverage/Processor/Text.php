<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Symfony\Component\Console\Output\OutputInterface;

class Text extends AbstractText
{
    public function __construct(OutputInterface $output, bool $showColors, OutputFile $targetFile = null)
    {
        parent::__construct($output, $showColors, false, $targetFile);
    }

    public static function getConsoleOptionName(): string
    {
        return 'text';
    }
}
