<?php

namespace AppBundle\Service\GameEngine;

use AppBundle\Domain\Service\GameEngine\GameEngineDaemonInterface;

/**
 * Class GameEngineDaemon
 *
 * @package AppBundle\Service\GameEngine
 */
class GameEngineDaemon implements GameEngineDaemonInterface
{
    const CONSOLE = __DIR__ . '/../../../../bin/console';
    const COMMAND = 'app:code-challenge:engine';

    /**
     * Starts the game engine daemon
     *
     * $ nohup php app/console app:code-challenge:engine > /dev/null 2> /dev/null &
     *
     * @param bool $force
     * @return void
     */
    public function start($force = false)
    {
        if ($force || !$this->isRunning()) {
            $command = 'nohup php ' . realpath(static::CONSOLE) . ' ' . static::COMMAND . ' > /dev/null 2> /dev/null &';
            @shell_exec($command);
        }
    }

    /**
     * Stops the game engine daemon
     *
     * @return void
     */
    public function stop()
    {
        $processId = $this->getProcessId();
        if ($processId > 0) {
            $command= 'kill -9 ' . $processId;
            @shell_exec($command);
        }
    }

    /**
     * Checks if the game engine daemon isd running
     *
     * @return bool true=running, 0=not running
     */
    public function isRunning()
    {
        $processId = $this->getProcessId();
        return (false !== $processId);
    }

    /**
     * Returns the process id of the game engine daemon
     *
     * @return int|false
     */
    public function getProcessId()
    {
        $result = $this->findProcess();
        if (!$result || !intval($result)) {
            return false;
        }

        return intval($result);
    }

    /**
     * Finds the process status and return the process ID
     *
     * $ ps ax -w | grep app:code-challenge:engine | grep -v 'grep' | awk '{print $1}'
     *
     * @return string|false
     */
    public function findProcess()
    {
        $command = 'ps ax -w'
            . ' | grep \'' . static::COMMAND . '\''
            . ' | grep -v \'grep\''
            . ' | awk \'{print $1}\'';

        $result = @shell_exec($command);
        if (empty($result)) {
            return false;
        }

        return $result;
    }
}
