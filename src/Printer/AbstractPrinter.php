<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractPrinter
{
    public function __construct(private readonly OutputInterface $output)
    {
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
