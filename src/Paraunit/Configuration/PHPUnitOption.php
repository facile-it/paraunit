<?php

declare(strict_types=1);

namespace Paraunit\Configuration;

/**
 * Class PHPUnitOption
 * @package Paraunit\Configuration
 */
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

    /**
     * PHPUnitOption constructor.
     * @param string $name
     * @param bool $hasValue
     * @param string | null $shortName
     */
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

    /**
     * @return string|null
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value = null)
    {
        $this->value = $value;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    public function hasValue(): bool
    {
        return $this->hasValue;
    }
}
