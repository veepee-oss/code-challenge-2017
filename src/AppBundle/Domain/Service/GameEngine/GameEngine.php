<?php

namespace AppBundle\Domain\Service\GameEngine;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;
use AppBundle\Domain\Service\MoveGhost\MoveGhostException;
use AppBundle\Domain\Service\MoveGhost\MoveGhostFactory;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use AppBundle\Domain\Service\MovePlayer\MovePlayerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class GameEngine
 *
 * @package AppBundle\Domain\Service\GameEngine
 */
class GameEngine
{
    /** @var  MovePlayerInterface */
    protected $movePlayer;

    /** @var  MoveGhostFactory */
    protected $moveGhostFactory;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * GameEngine constructor.
     *
     * @param MovePlayerInterface $movePlayer
     * @param MoveGhostFactory $moveGhostFactory
     */
    public function __construct(MovePlayerInterface $movePlayer, MoveGhostFactory $moveGhostFactory, LoggerInterface $logger)
    {
        $this->movePlayer = $movePlayer;
        $this->moveGhostFactory = $moveGhostFactory;
        $this->logger = $logger;
    }

    /**
     * Move all the players and ghosts of a game
     *
     * @param Game $game
     * @return bool TRUE if there are players alive
     */
    public function move(Game &$game)
    {
        $game->incMoves();

        $this->movePlayers($game);
        $this->moveGhosts($game);
        $this->createGhosts($game);

        if (!$game->arePlayersAlive()) {
            $game->endGame();
            return false;
        }

        return true;
    }

    /**
     * Move all the players
     *
     * @param Game $game
     * @return $this
     */
    protected function movePlayers(Game &$game)
    {
        /** @var Player[] $players */
        $players = $game->players();
        shuffle($players);

        foreach ($players as $player) {
            if ($player->status() == Player::STATUS_PLAYING) {
                try {
                    $this->movePlayer->movePlayer($player, $game);
                    if ($game->isGoalReached($player)) {
                        $player->wins();
                    }
                } catch (MovePlayerException $exc) {
                    $this->logger->error('Error moving player ' . $player->uuid());
                    $this->logger->error($exc);
                }
            }
        }

        return $this;
    }

    /**
     * Move all the ghosts
     *
     * @param Game $game
     * @return $this
     */
    protected function moveGhosts(Game &$game)
    {
        /** @var Ghost[] $ghosts */
        $ghosts = $game->ghosts();
        shuffle($ghosts);

        foreach ($ghosts as $ghost) {
            if (!$this->checkGhostKill($ghost, $game)) {
                try {
                    $moverService = $this->moveGhostFactory->locate($ghost);
                    if ($moverService->moveGhost($ghost, $game)) {
                        $this->checkGhostKill($ghost, $game);
                    }
                } catch (MoveGhostException $exc) {
                    $this->logger->error('Error moving ghost.');
                    $this->logger->error($exc);
                }
            }
        }

        return $this;
    }

    /**
     * Chacks if a ghost killed a player
     *
     * @param Ghost $ghost
     * @param Game $game
     * @return bool
     */
    protected function checkGhostKill(Ghost $ghost, Game& $game)
    {
        if ($ghost->isNeutralTime()) {
            return false;
        }

        $players = $game->players();
        shuffle($players);

        foreach ($players as $player) {
            if ($player->position()->y() == $ghost->position()->y()
                && $player->position()->x() == $ghost->position()->x()) {
                $game->removeGhost($ghost);
                $player->dies();
                return true;
            }
        }
        return false;
    }

    /**
     * Create new ghost if ghost rate reached or not enough ghosts
     *
     * @param Game $game
     * @return $this
     */
    protected function createGhosts(Game &$game)
    {
        if ($game->ghostRate() > 0 && $game->moves() % $game->ghostRate() == 0) {
            $this->createNewGhost($game);
        }

        while (count($game->ghosts()) < $game->minGhosts()) {
            $this->createNewGhost($game);
        }

        return $this;
    }

    /**
     * Create new ghost
     *
     * @param Game $game
     * @return $this
     */
    protected function createNewGhost(Game &$game, $type = Ghost::TYPE_SIMPLE)
    {
        $maze = $game->maze();
        do {
            $y = rand(1, $maze->height() - 2);
            $x = rand(1, $maze->width() - 2);
        } while ($maze[$y][$x]->getContent() != MazeCell::CELL_EMPTY);
        $game->addGhost(new Ghost($type, new Position($y, $x)));
        return $this;
    }
}
