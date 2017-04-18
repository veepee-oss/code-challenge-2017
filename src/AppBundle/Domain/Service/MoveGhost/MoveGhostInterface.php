<?php

namespace AppBundle\Domain\Service\MoveGhost;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Ghost\Ghost;

/**
 * Interface MoveGhostInterface
 *
 * @package AppBundle\Domain\Service\MoveGhost
 */
interface MoveGhostInterface
{
    /**
     * Moves the ghost
     *
     * @param Ghost $ghost
     * @param Game $game
     * @return bool true=successs, false=error
     * @throws MoveGhostException
     */
    public function moveGhost(Ghost& $ghost, Game $game);
}
