<?php

namespace AppBundle\Domain\Service\GameEngine;

/**
 * Interface GameEngineDaemonInterface
 *
 * @package AppBundle\Domain\Service\GameEngine
 */
interface GameEngineDaemonInterface
{
    /**
     * Starts the game engine daemon
     *
     * @param bool $force
     * @return void
     */
    public function start($force = false);

    /**
     * Stops the game engine daemon
     *
     * @return void
     */
    public function stop();

    /**
     * Checks if the game engine daemon isd running
     *
     * @return bool true=running, 0=not running
     */
    public function isRunning();

    /**
     * Returns the process id of the game engine daemon
     *
     * @return int|false
     */
    public function getProcessId();

    /**
     * Finds the process status and return the process ID
     *
     * @return string|false
     */
    public function findProcess();
}
