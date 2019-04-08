<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

class PHPUnitOption
{
    /** @var string */
    private $name;

    /** @var string|null */
    private $shortName;

    /** @var string|null */
    private $value;

    /** @var bool */
    private $hasValue;

    public function __construct(string $name, bool $hasValue = true, string $shortName = null)
    {
        $this->name = $name;
        $this->hasValue = $hasValue;
        $this->shortName = $shortName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value = null): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function hasValue(): bool
    {
        return $this->hasValue;
    }
}
