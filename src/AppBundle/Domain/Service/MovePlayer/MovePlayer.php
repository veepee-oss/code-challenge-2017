<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class MovePlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
abstract class MovePlayer implements MovePlayerInterface
{
    const STEP = 5;

    /**
     * Moves the player
     *
     * @param Player $player
     * @param Maze $maze
     * @return bool true=successs, false=error
     * @throws MovePlayerException
     */
    public function movePlayer(Player& $player, Maze $maze)
    {
        // Reads the next movement of the player: "up", "down", "left" or "right".
        $direction = $this->readNextMovement($player, $maze);

        // Computes the new position
        $position = $this->computeNewPosition($player->position(), $direction);
        if (!$this->validatePosition($position, $maze)) {
            return false;
        }

        $player->move($position);

        return true;
    }

    /**
     * Reads the next movemento of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Maze $maze
     * @return string The next movement
     * @throws MovePlayerException
     */
    abstract protected function readNextMovement(Player $player, Maze $maze);

    /**
     * Creates the request data to send to the player bot or api.
     *
     * @param Player $player
     * @param Maze $maze
     * @return string Request in json format
     */
    protected function createRequestData(Player $player, Maze $maze)
    {
        $pos = $player->position();
        $prev = $player->previous();

        $y1 = $pos->y() - static::STEP;
        $y2 = $pos->y() + static::STEP;
        $x1 = $pos->x() - static::STEP;
        $x2 = $pos->x() + static::STEP;

        if ($y1 < 0) {
            $y2 -= $y1;
            $y1 = 0;
        } elseif ($y2 >= $maze->height()) {
            $y1 -= ($pos->y() - $maze->height() + 1);
            $y2 = $maze->height() - 1;
        }

        if ($x1 < 0) {
            $x2 -= $x1;
            $x1 = 0;
        } elseif ($x2 >= $maze->width()) {
            $x1 -= ($pos->y() - $maze->width() + 1);
            $x2 = $maze->width() - 1;
        }

        $walls = array();
        for ($y = $y1; $y <= $y2; ++$y) {
            for ($x = $x1; $x <= $x2; ++$x) {
                if ($maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
                    $walls[] = array(
                        'y' => $y,
                        'x' => $x
                    );
                }
            }
        }

        $data = array(
            'player'    => array(
                'position'  => array(
                    'y'         => $pos->y(),
                    'x'         => $pos->x()
                ),
                'previous'  => array(
                    'y'         => $prev->y(),
                    'x'         => $prev->x()
                ),
                'area'      => array(
                    'y1'        => $y1,
                    'x1'        => $x1,
                    'y2'        => $y2,
                    'x2'        => $x2
                )
            ),
            'maze'      => array(
                'size'      => array(
                    'height'    => $maze->height(),
                    'width'     => $maze->width()
                ),
                'walls'     => $walls
            )
        );

        return json_encode($data);
    }

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
            case Player::DIRECTION_UP:
                $y--;
                break;

            case Player::DIRECTION_DOWN:
                $y++;
                break;

            case Player::DIRECTION_LEFT:
                $x--;
                break;

            case Player::DIRECTION_RIGHT:
                $y--;
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
