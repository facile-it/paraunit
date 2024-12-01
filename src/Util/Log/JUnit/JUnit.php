<?php

declare(strict_types=1);

namespace Paraunit\Util\Log\JUnit;

use DOMDocument;
use DOMElement;
use DOMException;
use Exception;
use Paraunit\Util\Log\Helpers\DOMDocumentAttributes;
use Paraunit\Util\Log\Interfaces\LogInterface;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Paraunit\Configuration\EnvVariables;

class JUnit implements LogInterface
{
    private const JUNIT_TEMP_DIRECTORY = 'junit_temp_dir';
    /** @var DOMDocument  */
    private $document;
    /** @var DOMDocumentAttributes  */
    private $documentAttributes;
    /** @var string  */
    private $inputFileName;
    /** @var string  */
    private $dirname;
    /** @var string  */
    private $basename;
    /** @var string  */
    private $extension;
    /** @var array  */
    private $files = [];
    /**
     * @var DOMElement[]
     */
    private $domElements = [];
    private $keysToCalculate = ['assertions', 'time', 'tests', 'errors', 'failures', 'skipped'];

    public function generateLogForTest(string $testFilename): string
    {
        return $this->init($this->getInputFileName(), $testFilename);
    }

    /**
     * Generates file where the log-junit report for the current $testFilename will be stored
     * returns CLI command '--log-junit='$file' that will be called from options array.
     */
    private function init(string $inputFilename, string $testFilename): string
    {
        $this->setDirname($inputFilename);
        $this->setExtension($inputFilename);
        $this->setBasename($inputFilename);
        $this->setDOMDocumentAttributes();

        //Generate file per test
        $file = $this->getDirname() .
            DIRECTORY_SEPARATOR .
            $this->getTempDirectory() .
            DIRECTORY_SEPARATOR .
            "{$this->getBasename()}_{$this->getRandomName($testFilename)}.{$this->getExtension()}";

        //Store the generated file in array to merge them later
        $this->files[] = $file;
        return '--log-junit=' . $file;
    }

    public function setDOMDocumentAttributes(): void
    {
        $this->documentAttributes = new DOMDocumentAttributes();
    }

    public function getDirname(): string
    {
        return $this->dirname;
    }

    public function setDirname(string $inputFileName): void
    {
        $this->dirname = dirname($inputFileName);
    }

    public function getTempDirectory(): array|string
    {
        $junitTempDir = getenv(EnvVariables::LOG_JUNIT_DIR);

        if ($junitTempDir === false) {
            return self::JUNIT_TEMP_DIRECTORY;
        }

        if (substr($junitTempDir, -1) !== DIRECTORY_SEPARATOR) {
            $junitTempDir .= DIRECTORY_SEPARATOR;
        }

        return $junitTempDir;
    }

    public function getBasename(): string
    {
        return $this->basename;
    }

    public function setBasename(string $inputFileName): void
    {
        $basename = basename($inputFileName, ".{$this->getExtension()}");
        $this->basename = $basename;
    }

    private function getRandomName(string $testFilename): string
    {
        return md5($testFilename);
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $inputFileName): void
    {
        $this->extension = pathinfo($inputFileName, PATHINFO_EXTENSION) !== ''
            ? pathinfo($inputFileName, PATHINFO_EXTENSION)
            : 'xml';
    }

    public function getInputFileName(): ?string
    {
        return $this->inputFileName ?? null;
    }

    public function setInputFileName(string $inputFileName): void
    {
        $this->inputFileName = $this->checkInputFileNameExtension($inputFileName);
    }

    private function checkInputFileNameExtension(string $inputFileName): string
    {
        return pathinfo($inputFileName, PATHINFO_EXTENSION) !== ''
            ? $inputFileName
            : $inputFileName . '.' . 'xml';
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function checkIfInitialized(): bool
    {
        if (!isset($this->inputFileName)) {
            return false;
        }
        return true;
    }

    /**
     * @throws DOMException
     */
    public function generate(): int|string
    {
        $finder = new Finder();
        $finder->files()
            ->name($this->getBasename() . '*')
            ->in(realpath($this->getTempDirectory()));

        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;

        $root = $this->document->createElement('testsuites');
        $baseSuite = $this->document->createElement('testsuite');

        foreach ($this->documentAttributes->getBaseSuiteAttributes() as $qualifiedName => $value) {
            $baseSuite->setAttribute($qualifiedName, $value);
        }

        $this->domElements['All Suites'] = $baseSuite;

        $root->appendChild($baseSuite);
        $this->document->appendChild($root);

        foreach ($finder as $file) {
            try {
                $xml = new SimpleXMLElement(file_get_contents($file->getRealPath()));
                $xmlArray = json_decode(json_encode($xml, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
                if (!empty($xmlArray)) {
                    $this->addTestSuites($baseSuite, $xmlArray);
                }
            } catch (Exception $exception) {
                return $exception->getMessage();
//                $output->writeln(sprintf('<error>Error in file %s: %s</error>', $file->getRealPath(), $exception->getMessage()));
            }
        }

        foreach ($this->domElements as $domElement) {
            if ($domElement->hasAttribute('parent')) {
                $domElement->removeAttribute('parent');
            }
        }

        $this->calculateTopLevelStats();
        $file = $this->getInputFileName();
        if (!is_dir(dirname($file)) && !mkdir($concurrentDirectory = dirname($file), 0777, true) && !is_dir(
                $concurrentDirectory
            )) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $this->document->save($this->getInputFileName());

        //Cleanup temp directory
        $this->deleteTempFiles();

        return 0;
    }

    private function addTestSuites(DOMElement $parent, array $testSuites): void
    {
        foreach ($testSuites as $testSuite) {
            if (empty($testSuite['@attributes']['name'])) {
                if (!empty($testSuite['testsuite'])) {
                    $this->addTestSuites($parent, $testSuite);
                }
                continue;
            }
            $name = $testSuite['@attributes']['name'];

            if (isset($this->domElements[$name])) {
                $element = $this->domElements[$name];
            } else {
                $element = $this->document->createElement('testsuite');
                $element->setAttribute('parent', $parent->getAttribute('name'));
                $attributes = $testSuite['@attributes'] ?? [];
                foreach ($attributes as $key => $value) {
                    $element->setAttribute($key, (string)$value);
                }
                $parent->appendChild($element);
                $this->domElements[$name] = $element;
            }

            if (!empty($testSuite['testsuite'])) {
                $children = isset($testSuite['testsuite']['@attributes']) ? [$testSuite['testsuite']] : $testSuite['testsuite'];
                $this->addTestSuites($element, $children);
            }

            if (!empty($testSuite['testcase'])) {
                $children = isset($testSuite['testcase']['@attributes']) ? [$testSuite['testcase']] : $testSuite['testcase'];
                $this->addTestCases($element, $children);
            }
        }
    }

    private function addTestCases(DOMElement $parent, array $testCases): void
    {
        foreach ($testCases as $testCase) {
            $attributes = $testCase['@attributes'] ?? [];
            if (empty($testCase['@attributes']['name'])) {
                continue;
            }
            $name = $testCase['@attributes']['name'];

            $element = $this->document->createElement('testcase');
            foreach ($attributes as $key => $value) {
                $element->setAttribute($key, (string)$value);
            }
            if (isset($testCase['failure']) || isset($testCase['warning']) || isset($testCase['error'])) {
                $this->addChildElements($testCase, $element);
            }
            $parent->appendChild($element);
            $this->domElements[$name] = $element;
        }
    }

    private function addChildElements(array $tree, DOMElement $element): void
    {
        foreach ($tree as $key => $value) {
            if ($key === '@attributes') {
                continue;
            }
            $child = $this->document->createElement($key);
            $child->nodeValue = $value;
            $element->appendChild($child);
        }
    }

    private function calculateTopLevelStats(): void
    {
        /** @var DOMElement $topNode */
        $suites = $this->document->getElementsByTagName('testsuites')->item(0);
        $topNode = $suites->firstChild;
        if ($topNode->hasChildNodes()) {
            $stats = array_flip($this->keysToCalculate);
            $stats = array_map(function ($_value) {
                return 0;
            }, $stats);
            foreach ($topNode->childNodes as $child) {
                $attributes = $child->attributes;
                foreach ($attributes as $key => $value) {
                    if (in_array($key, $this->keysToCalculate, true)) {
                        $stats[$key] += $value->nodeValue;
                    }
                }
            }

            foreach ($stats as $key => $value) {
                $topNode->setAttribute($key, (string)$value);
            }
        }
    }

    private function deleteTempFiles(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove(realpath($this->getTempDirectory()));
    }
}