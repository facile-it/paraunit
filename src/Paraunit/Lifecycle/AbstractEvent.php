<?php

namespace Paraunit\Lifecycle;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class AbstractEvent
 * @package Paraunit\Lifecycle
 */
abstract class AbstractEvent extends Event
{
    /** @var array */
    private $context;

    /**
     * AbstractEvent constructor.
     * @param array $context
     */
    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    /**
     * @param $contextParameterName
     *
     * @return bool
     */
    public function has(string $contextParameterName): bool
    {
        return isset($this->context[$contextParameterName]);
    }

    /**
     * @param string $contextParameterName
     * @return mixed
     * @throws \LogicException If parameter is not found
     */
    public function get(string $contextParameterName)
    {
        if (! $this->has($contextParameterName)) {
            throw new \LogicException('Cannot find parameter: ' . $contextParameterName);
        }

        return $this->context[$contextParameterName];
    }
}
