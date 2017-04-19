<?php

namespace AppBundle\Service\GameEngine;

/**
 * Class GameEngineDaemon
 *
 * @package AppBundle\Service\GameEngine
 */
class GameEngineDaemon
{
    const CONSOLE = __DIR__ . '/../../../../bin/console';
    const COMMAND = 'app:code-challenge:engine';

    /**
     * Starts the game engine daemon
     */
    public function start()
    {
        if (!$this->isRunning()) {
            $command = 'php ' . realpath(static::CONSOLE) . ' ' . static::COMMAND . ' > /dev/null 2>/dev/null &';
            @shell_exec($command);
        }
    }

    /**
     * Stops the game engine daemon
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
        $command = 'ps ax'
            . ' | grep \'' . static::COMMAND . '\''
            . ' | grep -v \'grep\''
            . ' | awk \'{print $1}\'';

        $result = @shell_exec($command);
        if (empty($result) || !intval($result)) {
            return false;
        }

        return intval($result);
    }
}
