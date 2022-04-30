<?php

declare(strict_types=1);

namespace Paraunit\Runner;

use Paraunit\Configuration\PHPUnitConfig;
use PHPUnit\Util\Xml\Loader;

class ChunkFile
{
    /** @var Loader */
    private $xmlLoader;

    /** @var PHPUnitConfig */
    private $phpunitConfig;

    public function __construct(PHPUnitConfig $phpunitConfig)
    {
        $this->xmlLoader = new Loader();
        $this->phpunitConfig = $phpunitConfig;
    }

    /**
     * @param array<string> $files
     */
    public function createChunkFile(
        int $chunkNumber,
        array $files
    ): string {
        $fileFullPath = $this->phpunitConfig->getFileFullPath();
        $document = $this->xmlLoader->loadFile($fileFullPath);
        $xpath = new \DOMXPath($document);

        $nodeList = $xpath->query('testsuites');

        if (! $nodeList) {
            throw new \InvalidArgumentException('No testsuites node found in the PHPUnit configuration in ' . $fileFullPath);
        }

        foreach ($nodeList as $testSuitesNode) {
            if (! $testSuitesNode instanceof \DOMElement) {
                throw new \InvalidArgumentException('Invalid DOM subtype in PHPUnit configuration, expeding \DOMElement, got ' . get_class($testSuitesNode));
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
            break;
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
}
