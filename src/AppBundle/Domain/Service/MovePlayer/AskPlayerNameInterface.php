<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Interface AskPlayerNameInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface AskPlayerNameInterface
{
    /**
     * Asks for the name of the player
     *
     * @param Player $player
     * @param Game $game
     * @return array['name', 'email'] The player name and email
     * @throws MovePlayerException
     */
    public function askPlayerName(Player $player, Game $game = null);
}
