<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class AbstractEvent
 * @package Paraunit\Lifecycle
 */
abstract class AbstractEvent extends Event
{
    /** @var  array */
    protected $context = array();

    /**
     * @param $contextParameterName
     *
     * @return bool
     */
    public function has($contextParameterName)
    {
        return isset($this->context[$contextParameterName]);
    }

    /**
     * @param $contextParameterName
     *
     * @return mixed
     */
    public function get($contextParameterName)
    {
        if (! $this->has($contextParameterName)) {
            throw new \LogicException('Cannot find parameter: ' . $contextParameterName);
        }

        return $this->context[$contextParameterName];
    }
}
