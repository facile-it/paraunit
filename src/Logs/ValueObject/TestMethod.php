<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

use PHPUnit\Event\Code\TestMethod as PHPUnitTestMethod;

class TestMethod extends Test
{
    private const CLASS_NAME = 'className';

    private const METHOD_NAME = 'methodName';

    public function __construct(
        public readonly string $className,
        public readonly string $methodName,
    ) {
        parent::__construct($this->className . '::' . $this->methodName);
    }

    public static function fromPHPUnitTestMethod(PHPUnitTestMethod $test): self
    {
        return new self($test->className(), $test->methodName());
    }

    /**
     * @return array{className: string, methodName: string}
     */
    public function jsonSerialize(): array
    {
        return [
            self::CLASS_NAME => $this->className,
            self::METHOD_NAME => $this->methodName,
        ];
    }

    public static function deserialize(mixed $data): Test
    {
        self::validate($data);

        return new self($data[self::CLASS_NAME], $data[self::METHOD_NAME]);
    }

    /**
     * @psalm-assert array{className: string, methodName: string} $data
     */
    private static function validate(mixed $data): void
    {
        if (! is_array($data)) {
            throw self::invalidDeserializeInput($data);
        }

        if (! is_string($data[self::CLASS_NAME] ?? false)) {
            throw new \InvalidArgumentException('className field missing or invalid');
        }

        if (! is_string($data[self::METHOD_NAME] ?? false)) {
            throw new \InvalidArgumentException('className field missing or invalid');
        }
    }
}
