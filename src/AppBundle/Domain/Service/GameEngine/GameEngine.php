<?php

namespace AppBundle\Domain\Service\GameEngine;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\MovePlayer\MovePlayerFactory;
use Davamigo\HttpClient\Domain\HttpClient;

/**
 * Class GameEngine
 *
 * @package AppBundle\Domain\Service\GameEngine
 */
class GameEngine
{
    /** @var  MovePlayerFactory */
    protected $factory;

    /**
     * GameEngine constructor.
     *
     * @param MovePlayerFactory $factory
     */
    public function __construct(MovePlayerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Move all the players of a game
     *
     * @param Game $game
     * @return bool
     */
    public function movePlayers(Game &$game)
    {
        $result = false;

        /** @var Player[] $players */
        $players = $game->players();
        foreach ($players as $player) {
            $service = $this->factory->locate($player);
            if ($service->movePlayer($player, $game->maze())) {
                $result = true;
            }
        }

        return $result;
    }
}
