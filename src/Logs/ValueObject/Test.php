<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

use PHPUnit\Event\Code\Test as PHPUnitTest;
use PHPUnit\Event\Code\TestMethod as PHPUnitTestMethod;

class Test implements \JsonSerializable
{
    public function __construct(public readonly string $name) {}

    final public static function fromPHPUnitTest(PHPUnitTest $test): self
    {
        if ($test instanceof PHPUnitTestMethod) {
            return TestMethod::fromPHPUnitTestMethod($test);
        }

        return new self($test->name());
    }

    /**
     * @return string|array<string, scalar>
     */
    public function jsonSerialize(): string|array
    {
        return $this->name;
    }

    public static function deserialize(mixed $data): self
    {
        if (is_array($data)) {
            return TestMethod::deserialize($data);
        }

        if (is_string($data)) {
            return new self($data);
        }

        throw self::invalidDeserializeInput($data);
    }

    public static function unknown(): self
    {
        return new self('[UNKNOWN]');
    }

    final protected static function invalidDeserializeInput(mixed $data): \InvalidArgumentException
    {
        return new \InvalidArgumentException('Unable to deserialize from ' . get_debug_type($data));
    }
}
