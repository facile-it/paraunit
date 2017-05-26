<?php
declare(strict_types=1);

namespace Paraunit\TestResult;

/**
 * Class TestResultList
 * @package Paraunit\TestResult
 */
class TestResultList
{
    /** @var  TestResultContainer[] */
    private $testResultContainers;

    public function __construct()
    {
        $this->testResultContainers = [];
    }

    public function addContainer(TestResultContainer $container)
    {
        $this->testResultContainers[] = $container;
    }

    /**
     * @return TestResultContainer[]
     */
    public function getTestResultContainers(): array
    {
        return $this->testResultContainers;
    }
}
