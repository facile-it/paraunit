<?php

declare(strict_types=1);

namespace Paraunit\Coverage\Processor;

use Paraunit\Configuration\OutputFile;
use Paraunit\Coverage\PhpUnitFacadeFactory;
use Symfony\Component\Console\Output\OutputInterface;

class TextSummary extends AbstractText
{
    public function __construct(
        PhpUnitFacadeFactory $facadeFactory,
        OutputInterface $output,
        bool $showColors,
        ?OutputFile $targetFile = null,
    ) {
        parent::__construct($facadeFactory, $output, $showColors, true, $targetFile);
    }

    public static function getConsoleOptionName(): string
    {
        return 'text-summary';
    }
}
