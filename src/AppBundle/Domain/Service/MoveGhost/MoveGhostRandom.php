<?php

namespace AppBundle\Domain\Service\MoveGhost;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class MoveGhostRandom
 *
 * @package AppBundle\Domain\Service\MoveGhost
 */
class MoveGhostRandom extends MoveGhost
{
    /**
     * Computes the next movement of the ghost: "up", "down", "left" or "right".
     *
     * @param Ghost $ghost
     * @param Game $game
     * @return string The next movement
     * @throws MoveGhostException
     */
    protected function computeNextMovement(Ghost $ghost, Game $game)
    {
        // Extract some vars
        $maze = $game->maze();
        $height = $maze->height();
        $width = $maze->width();
        $pos = $ghost->position();
        $dir = $ghost->direction();

        // Available moves
        $moves = array(
            MazeObject::DIRECTION_UP,
            MazeObject::DIRECTION_RIGHT,
            MazeObject::DIRECTION_DOWN,
            MazeObject::DIRECTION_LEFT
        );

        // If not stopped
        if ($dir != MazeObject::DIRECTION_STOP) {
            // 20% probability of turning right or left
            $turn = (rand(0, 9) < 2);
            if ($turn) {
                $add = (rand(0, 1) == 0) ? 1 : 3;
                $dir = $moves[($add + array_search($dir, $moves)) % 4];
            }

            // Test movement
            if ($this->testMove($maze, $height, $width, $pos, $dir)) {
                return $dir;
            }

            unset($moves[array_search($dir, $moves)]);
        }

        shuffle($moves);
        foreach ($moves as $dir) {
            if ($this->testMove($maze, $height, $width, $pos, $dir)) {
                break;
            }
        }

        return $dir;
    }

    /**
     * Test if move can be done
     *
     * @param Maze $maze
     * @param int $height
     * @param int $width
     * @param Position $pos
     * @param string $dir
     * @return bool
     */
    private function testMove(Maze $maze, $height, $width, Position $pos, $dir)
    {
        $y = $pos->y();
        $x = $pos->x();
        switch ($dir) {
            case MazeObject::DIRECTION_UP:
                if (--$y < 0) {
                    return false;
                }
                break;

            case MazeObject::DIRECTION_DOWN:
                if (++$y >= $height) {
                    return false;
                }
                break;

            case MazeObject::DIRECTION_LEFT:
                if (--$x < 0) {
                    return false;
                }
                break;

            case MazeObject::DIRECTION_RIGHT:
                if (++$x >= $width) {
                    return false;
                }
                break;

            default:
                return false;
        }

        $content = $maze[$y][$x]->getContent();
        if ($content == MazeCell::CELL_WALL
            || $content == MazeCell::CELL_GOAL) {
            return false;
        }

        return true;
    }
}
