<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class MovePlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
abstract class MovePlayer implements MovePlayerInterface
{
    /**
     * Informs a player the game is starnign and asks his name
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=successs, false=error
     * @throws MovePlayerException
     */
    public function startGame(Player& $player, Game $game)
    {
        $name = $this->getPlayerName($player, $game);
        $player->setName($name);
        return true;
    }

    /**
     * Moves the player
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=successs, false=error
     * @throws MovePlayerException
     */
    public function movePlayer(Player& $player, Game $game)
    {
        // Reads the next movement of the player: "up", "down", "left" or "right".
        $direction = $this->readNextMovement($player, $game);

        // Computes the new position
        $position = $this->computeNewPosition($player->position(), $direction);
        if (!$this->validatePosition($position, $game->maze())) {
            return false;
        }

        $player->move($position);

        return true;
    }

    /**
     * Asks for the name of the player
     *
     * @param Player $player
     * @param Game $game
     * @return string The player name
     * @throws MovePlayerException
     */
    abstract protected function getPlayerName(Player $player, Game $game);

    /**
     * Reads the next movemento of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Game $game
     * @return string The next movement
     * @throws MovePlayerException
     */
    abstract protected function readNextMovement(Player $player, Game $game);


    /**
     * Computes the new position for a movement
     *
     * @param Position $position
     * @param string $direction
     * @return Position
     */
    protected function computeNewPosition(Position $position, $direction)
    {
        $y = $position->y();
        $x = $position->x();
        switch ($direction) {
            case MazeObject::DIRECTION_UP:
                $y--;
                break;

            case MazeObject::DIRECTION_DOWN:
                $y++;
                break;

            case MazeObject::DIRECTION_LEFT:
                $x--;
                break;

            case MazeObject::DIRECTION_RIGHT:
                $x++;
                break;
        }

        return new Position($y, $x);
    }

    /**
     * Validates the position in the map
     *
     * @param Position $position
     * @param Maze $maze
     * @return bool
     */
    protected function validatePosition(Position $position, Maze $maze)
    {
        $y = $position->y();
        $x = $position->x();

        if ($y < 0 || $y >= $maze->height()) {
            return false;
        }

        if ($x < 0 || $x >= $maze->width()) {
            return false;
        }

        if ($maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
            return false;
        }

        return true;
    }
}
