<?php

namespace Paraunit\Util\Log\Interfaces;

interface LogInterface
{
    public function setInputFileName(string $inputFileName): void;

    public function checkIfInitialized(): bool;

    public function generateLogForTest(string $testFilename): string;

    public function getExtension(): string;

    public function setExtension(string $inputFileName): void;

    public function getDirname(): string;

    public function setDirname(string $inputFileName): void;

    public function getBasename(): string;

    public function setBasename(string $inputFileName): void;

    public function generate(): int|string;

    public function getFiles(): array;

    public function getInputFileName(): ?string;

    public function getTempDirectory(): array|string;
}