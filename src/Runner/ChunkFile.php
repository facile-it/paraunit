<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Process\Process;
use PHPUnit\Util\Xml\Loader;

class ChunkFile
{
    private readonly Loader $xmlLoader;

    public function __construct(private readonly PHPUnitConfig $phpunitConfig)
    {
        /** @psalm-suppress InternalClass */
        $this->xmlLoader = new Loader();
    }

    /**
     * @param array<int, string> $files
     */
    public function createChunkFile(
        int $chunkNumber,
        array $files
    ): string {
        $fileFullPath = $this->phpunitConfig->getFileFullPath();
        /** @psalm-suppress InternalMethod */
        $document = $this->xmlLoader->loadFile($fileFullPath);
        $xpath = new \DOMXPath($document);

        $nodeList = $xpath->query('testsuites');

        if (! $nodeList) {
            throw new \InvalidArgumentException('No testsuites node found in the PHPUnit configuration in ' . $fileFullPath);
        }

        $nodeList = iterator_to_array($nodeList);
        $testSuitesNode = array_shift($nodeList);

        if (! $testSuitesNode instanceof \DOMNode) {
            throw new \InvalidArgumentException('Expecting \DOMElement, got null');
        }

        $nameAttribute = $document->createAttribute('name');
        $nameAttribute->value = "Tests Suite $chunkNumber";

        $newTestSuiteNode = $document->createElement('testsuite');
        $newTestSuiteNode->appendChild($nameAttribute);

        foreach ($files as $file) {
            $newTestFileNode = $document->createElement('file', $file);
            $newTestSuiteNode->appendChild($newTestFileNode);
        }

        $newTestSuitesNode = $document->createElement('testsuites');
        $newTestSuitesNode->appendChild($newTestSuiteNode);

        $parentNode = $testSuitesNode->parentNode;
        if ($parentNode instanceof \DOMElement) {
            $parentNode->replaceChild($newTestSuitesNode, $testSuitesNode);
        }

        $chunkFileName = $this->getChunkFileName($fileFullPath, $chunkNumber);
        $document->save($chunkFileName);

        return $chunkFileName;
    }

    public function getChunkFileName(string $fileName, int $chunkNumber): string
    {
        $dirname = dirname($fileName);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $baseName = basename($fileName, ".{$extension}");

        return $dirname . DIRECTORY_SEPARATOR . "{$baseName}_{$chunkNumber}.{$extension}";
    }

    public function deleteChunkFile(Process $process): void
    {
        $filename = $process->getFilename();
        if (file_exists($filename)) {
            unlink($filename);
        }
    }
}
