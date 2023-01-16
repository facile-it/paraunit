<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class PHPUnitOption
{
    private ?string $value = null;

    public function __construct(
        private readonly string $name,
        private readonly bool $hasValue = true,
        private readonly ?string $shortName = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setValue(string $value = null): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @psalm-assert-if-true string $this->value
     * @psalm-assert-if-true string $this->getValue()
     */
    public function hasValue(): bool
    {
        return $this->hasValue;
    }
}
