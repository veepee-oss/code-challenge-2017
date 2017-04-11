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

        foreach ($players as $player) {
            if ($player->status() == Player::STATUS_PLAYING) {
                $moverService = $this->factory->locate($player);
                if ($moverService->movePlayer($player, $game->maze())) {
                    $moved = true;
                    if ($this->checkGoal($player, $game->maze()->goal())) {
                        $winner = true;
                    }
                }
            }
        }

        if ($winner || $looser) {
            if (!$this->$this->checkSomePlayersAlive($game)) {
                $game->stopPlaying();
            }
        }

        return $moved || $winner || $looser;
    }

    /**
     * Checks the goal
     *
     * @param Player $player
     * @param Position $goal
     * @return bool
     */
    protected function checkGoal(Player& $player, Position $goal)
    {
        $pos = $player->position();
        if ($pos->y() == $goal->y() && $pos->x() == $goal->x()) {
            $player->winner();
            return true;
        }
        return false;
    }

    /**
     * Check if there are at least one player alive
     *
     * @param Game $game
     * @return bool
     */
    protected function checkSomePlayersAlive(Game $game)
    {
        /** @var Player[] $players */
        $players = $game->players();
        foreach ($players as $player) {
            if ($player->status() == Player::STATUS_PLAYING) {
                return true;
            }
        }
        return false;
    }
}
