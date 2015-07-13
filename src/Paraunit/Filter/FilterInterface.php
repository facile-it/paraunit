<?php

namespace Paraunit\Filter;

use Symfony\Component\Process\Process;

/**
 * Class Filter
 * @package Paraunit\Filter
 */
interface FilterInterface
{
    /**
     * @return mixed
     */
    function getFiles();
}
