<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractPrinter
{
    /** @var OutputInterface */
    private $output;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}
