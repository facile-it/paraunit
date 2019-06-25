<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractPrinter
{
    /** @var OutputInterface */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
