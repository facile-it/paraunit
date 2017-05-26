<?php
declare(strict_types=1);

namespace Paraunit\Process;

/**
 * Interface OutputAwareInterface.
 */
interface OutputAwareInterface
{
    public function getOutput(): string;
}
