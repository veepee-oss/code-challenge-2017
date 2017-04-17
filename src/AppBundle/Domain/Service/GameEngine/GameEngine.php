<?php

namespace AppBundle\Domain\Service\GameEngine;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;
use AppBundle\Domain\Service\MovePlayer\MovePlayerFactory;

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
        $moved = false;
        $winner = false;
        $looser = false;

        /** @var Player[] $players */
        $players = $game->players();
        shuffle($players);

        $game->incMoves();
        foreach ($players as $player) {
            if ($player->status() == Player::STATUS_PLAYING) {
                $moverService = $this->factory->locate($player);
                if ($moverService->movePlayer($player, $game)) {
                    $moved = true;
                    if ($game->isGoalReached($player)) {
                        $player->winner();
                        $winner = true;
                    }
                }
            }
        }

        if ($winner || $looser) {
            if (!$game->arePlayersAlive()) {
                $game->endGame();
            }
        }

        return $moved || $winner || $looser;
    }
}
