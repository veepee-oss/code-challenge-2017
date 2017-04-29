<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Interface ValidatePlayerInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface ValidatePlayerInterface
{
    /**
     * Validates the player asking for the name
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function validatePlayer(Player& $player, Game $game = null);
}
