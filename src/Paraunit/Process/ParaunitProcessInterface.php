<?php
namespace Paraunit\Process;


interface ParaunitProcessInterface
{

    /**
     * @return bool
     */
    public function isTerminated();

    /**
     * @return string
     */
    public function getUniqueId();

    /**
     * @return string
     */
    public function getCommandLine();

    /**
     * @return mixed
     */
    public function start();

    /**
     * @return $this
     */
    public function restart();

    /**
     * @return $this
     */
    public function reset();

    /**
     * @return bool
     */
    public function isRunning();

    /**
     * @return int
     */
    public function getExitCode();
}
