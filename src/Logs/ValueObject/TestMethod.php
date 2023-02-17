<?php

declare(strict_types=1);

namespace Paraunit\Logs\ValueObject;

use PHPUnit\Event\Code\TestMethod as PHPUnitTestMethod;

class TestMethod extends Test
{
    private const CLASS_NAME = 'className';

    private const METHOD_NAME = 'methodName';

    private const FULL_NAME = 'fullName';

    public function __construct(
        public readonly string $className,
        public readonly string $methodName,
        string $fullName = null,
    ) {
        parent::__construct($fullName ?? $this->className . '::' . $this->methodName);
    }

    public static function fromPHPUnitTestMethod(PHPUnitTestMethod $test): self
    {
        return new self($test->className(), $test->methodName(), $test->nameWithClass());
    }

    /**
     * @return array{className: string, methodName: string, fullName: string}
     */
    public function jsonSerialize(): array
    {
        return [
            self::CLASS_NAME => $this->className,
            self::METHOD_NAME => $this->methodName,
            self::FULL_NAME => $this->name,
        ];
    }

    public static function deserialize(mixed $data): Test
    {
        self::validate($data);

        return new self($data[self::CLASS_NAME], $data[self::METHOD_NAME], $data[self::FULL_NAME]);
    }

    /**
     * @psalm-assert array{className: string, methodName: string, fullName: string, ...} $data
     */
    private static function validate(mixed $data): void
    {
        if (! is_array($data)) {
            throw self::invalidDeserializeInput($data);
        }

        self::assertPropertyIsString(self::CLASS_NAME, $data);
        self::assertPropertyIsString(self::METHOD_NAME, $data);
        self::assertPropertyIsString(self::FULL_NAME, $data);
    }

    /**
     * @template T of string
     *
     * @param T $property
     * @param mixed[] $data
     * @psalm-assert array{T: string, ...} $data
     */
    private static function assertPropertyIsString(string $property, array $data): void
    {
        if (! is_string($data[$property] ?? false)) {
            throw new \InvalidArgumentException($property . ' field missing or invalid');
        }
    }
}
