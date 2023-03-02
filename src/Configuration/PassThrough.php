<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class PassThrough
{
    /** @var list<string> */
    public readonly array $options;

    /**
     * @param string[]|null $options
     */
    public function __construct(?array $options = [])
    {
        $this->options = array_values($options ?? []);
    }
}
