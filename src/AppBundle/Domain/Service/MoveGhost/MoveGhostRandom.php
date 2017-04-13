<?php

namespace AppBundle\Domain\Service\MoveGhost;

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
class MoveGhostRandom implements MoveGhostInterface
{
    /**
     * Moves the ghost
     *
     * @param Ghost $ghost
     * @param Maze $maze
     * @return bool true=successs, false=error
     * @throws MoveGhostException
     */
    public function moveGhost(Ghost& $ghost, Maze $maze)
    {
        // Extract some vars
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
            if (!$this->testMove($maze, $height, $width, $pos, $dir)) {
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
        switch ($dir) {
            case MazeObject::DIRECTION_UP:
                $new = new Position($pos->y() -  1, $pos->x());
                if ($new->y() < 0) {
                    return false;
                }
                break;

            case MazeObject::DIRECTION_DOWN:
                $new = new Position($pos->y() +  1, $pos->x());
                if ($new->y() >= $height) {
                    return false;
                }
                break;

            case MazeObject::DIRECTION_LEFT:
                $new = new Position($pos->y(), $pos->x() - 1);
                if ($new->x() < 0) {
                    return false;
                }
                break;

            case MazeObject::DIRECTION_RIGHT:
                $new = new Position($pos->y(), $pos->x() + 1);
                if ($new->x() >= $width) {
                    return false;
                }
                break;

            default:
                return false;
        }

        if ($maze[$new->y()][$new->x()]->getContent() == MazeCell::CELL_WALL) {
            return false;
        }

        return true;
    }
}
