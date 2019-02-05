<?php

declare(strict_types=1);

namespace Paraunit\Process;

interface ProcessFactoryInterface
{
    public function create(string $testFilePath): AbstractParaunitProcess;
}
