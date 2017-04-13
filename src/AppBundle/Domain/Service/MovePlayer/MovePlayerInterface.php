<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Interface MovePlayerInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface MovePlayerInterface
{
    /**
     * Moves the player
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=successs, false=error
     * @throws MovePlayerException
     */
    public function movePlayer(Player& $player, Game $game);
}
