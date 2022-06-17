<?php

declare(strict_types=1);

namespace Paraunit\Parser\DTO;

class Test
{
    public function __construct(public readonly string $name)
    {
    }
}
