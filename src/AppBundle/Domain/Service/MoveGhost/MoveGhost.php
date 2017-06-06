<?php

namespace AppBundle\Domain\Service\MoveGhost;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;
use AppBundle\Domain\Entity\Position\Direction;
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
        // Computes the next movement of the ghost: "up", "down", "left" or "right".
        $direction = $this->computeNextMovement($ghost, $game);

        // Computes the new position
        $position = $ghost->position();
        $y = $position->y();
        $x = $position->x();
        switch ($direction) {
            case Direction::UP:
                $y--;
                break;

            case Direction::DOWN:
                $y++;
                break;

            case Direction::LEFT:
                $x--;
                break;

            case Direction::RIGHT:
                $x++;
                break;
        }

        $position = new Position($y, $x);

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
