<?php

namespace AppBundle\Domain\Service\MoveGhost;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Abstract class MoveGhost
 *
 * @package AppBundle\Domain\Service\MoveGhost
 */
abstract class MoveGhost implements MoveGhostInterface
{
    /**
     * Moves the ghost
     *
     * @param Ghost $ghost
     * @param Game $game
     * @return bool true=successs, false=error
     * @throws MoveGhostException
     */
    public function moveGhost(Ghost& $ghost, Game $game)
    {
        echo sprintf(PHP_EOL . 'Ghost at [%02d, %02d] << ', $ghost->position()->x(), $ghost->position()->y());
        echo sprintf('from [%02d, %02d] << ', $ghost->previous()->x(), $ghost->previous()->y());
        echo sprintf('direction [%s]' . PHP_EOL, $ghost->direction());

        // Computes the next movement of the ghost: "up", "down", "left" or "right".
        $direction = $this->computeNextMovement($ghost, $game);

        echo sprintf('New Direction [%s] ', $direction);

        // Computes the new position
        $position = $ghost->position();
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

        $position = new Position($y, $x);

        echo sprintf('>> Move to [%02d, %02d]' . PHP_EOL, $position->x(), $position->y());

        $ghost->move($position);
        return true;
    }

    /**
     * Computes the next movement of the ghost: "up", "down", "left" or "right".
     *
     * @param Ghost $ghost
     * @param Game $game
     * @return string The next movement
     * @throws MoveGhostException
     */
    abstract protected function computeNextMovement(Ghost $ghost, Game $game);
}
