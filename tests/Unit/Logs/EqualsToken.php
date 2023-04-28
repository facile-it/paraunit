<?php

declare(strict_types=1);

namespace Tests\Unit\Logs;

use Prophecy\Argument\Token\TokenInterface;
use Prophecy\Util\StringUtil;

class EqualsToken implements TokenInterface, \Stringable
{
    private readonly StringUtil $util;

    private string $string;

    public function __construct(private readonly mixed $value, StringUtil $util = null)
    {
        $this->util = $util ?? new StringUtil();
    }

    /**
     * Scores 11 if argument matches preset value.
     */
    public function scoreArgument($argument): bool|int
    {
        return $argument == $this->value ? 11 : false;
    }

    public function isLast(): bool
    {
        return false;
    }

    /**
     * Returns string representation for token.
     */
    public function __toString(): string
    {
        if (! isset($this->string)) {
            $this->string = sprintf('equals(%s)', $this->util->stringify($this->value));
        }

        return $this->string;
    }
}
