<?php

declare(strict_types=1);

namespace Paraunit\Printer;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractPrinter
 * @package Paraunit\Printer
 */
abstract class AbstractPrinter
{
    /** @var OutputInterface */
    private $output;

    /**
     * AbstractPrinter constructor.
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
