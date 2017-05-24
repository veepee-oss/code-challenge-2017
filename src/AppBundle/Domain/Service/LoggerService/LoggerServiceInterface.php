<?php

namespace AppBundle\Domain\Service\LoggerService;

/**
 * Interface LoggerServiceInterface
 *
 * @package AppBundle\Domain\Service\LoggerService
 */
interface LoggerServiceInterface
{
    /**
     * Saves the data of the request
     *
     * @param string $gameUuid
     * @param string $playerUuid
     * @param array $data
     * @return void
     */
    public function log(
        $gameUuid,
        $playerUuid,
        array $data
    );

    /**
     * Clears the log of a game
     *
     * @param string $gameUuid
     * @param string $playerUuid
     * @return void
     */
    public function clear($gameUuid, $playerUuid = null);

    /**
     * Reads the log of a game
     *
     * @param string $gameUuid
     * @param string $playerUuid
     * @return array The raw data of the log
     */
    public function read($gameUuid, $playerUuid = null);
}
