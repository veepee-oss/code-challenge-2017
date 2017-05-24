<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Interface AskNextMovementInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface AskNextMovementInterface
{
    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Game $game
     * @return string The next movement
     * @throws MovePlayerException
     */
    public function askNextMovement(Player $player, Game $game);
}
