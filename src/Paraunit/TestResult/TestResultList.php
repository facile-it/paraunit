<?php

namespace Paraunit\TestResult;

/**
 * Class TestResultList
 * @package Paraunit\TestResult
 */
class TestResultList
{
    /** @var  DumbTestResultContainer[] */
    private $testResultContainers;

    public function __construct()
    {
        $this->testResultContainers = array();
    }

    /**
     * @param DumbTestResultContainer $container
     */
    public function addContainer(DumbTestResultContainer $container)
    {
        $this->testResultContainers[] = $container;
    }

    /**
     * @return DumbTestResultContainer[]
     */
    public function getTestResultContainers()
    {
        return $this->testResultContainers;
    }
}
