<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Player\Player;

/**
 * Class MovePlayerFactory
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MovePlayerFactory
{
    /** @var MovePlayer[] */
    protected $services;

    /**
     * MovePlayerFactory constructor.
     *
     * @param MovePlayer[] $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * Locates the right service to move a player
     *
     * @param Player $player
     * @return MovePlayer
     * @throws MovePlayerException
     */
    public function locate(Player $player)
    {
        if (!array_key_exists($player->type(), $this->services)) {
            throw new MovePlayerException('Move player service not found for class ' . get_class($player));
        }

        return $this->services[$player->type()];
    }
}
