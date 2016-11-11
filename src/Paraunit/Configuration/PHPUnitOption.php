<?php

namespace Paraunit\Configuration;

/**
 * Class PHPUnitOption
 * @package Paraunit\Configuration
 */
class PHPUnitOption
{
    /** @var string */
    private $name;
    
    /** @var string */
    private $shortName;
    
    /** @var string */
    private $value;
    
    /** @var bool */
    private $hasValue;

    /**
     * PHPUnitOption constructor.
     * @param string $name
     * @param bool $hasValue
     * @param string | null $shortName
     */
    public function __construct($name, $hasValue = true, $shortName = null)
    {
        $this->name = $name;
        $this->hasValue = $hasValue;
        $this->shortName = $shortName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return boolean
     */
    public function hasValue()
    {
        return $this->hasValue;
    }
}
