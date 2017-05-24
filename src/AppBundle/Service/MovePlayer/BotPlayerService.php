<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use AppBundle\Domain\Service\MovePlayer\AskPlayerNameInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use AppBundle\Domain\Service\MovePlayer\PlayerRequestInterface;

/**
 * Class BotPlayerService
 *
 * @package AppBundle\Service\MovePlayer
 */
class BotPlayerService implements AskPlayerNameInterface, AskNextMovementInterface
{
    /** @var PlayerRequestInterface */
    protected $playerRequest;

    /**
     * BotPlayerService constructor.
     *
     * @param PlayerRequestInterface $playerRequest
     */
    public function __construct(PlayerRequestInterface $playerRequest)
    {
        $this->playerRequest = $playerRequest;
    }

    /**
     * Asks for the name of the player
     *
     * @param Player $player
     * @param Game $game
     * @return array['name', 'email'] The player name and email
     * @throws MovePlayerException
     */
    public function askPlayerName(Player $player, Game $game = null)
    {
        // TODO
        return null;
    }

    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Game $game
     * @return string The next movement
     * @throws MovePlayerException
     */
    public function askNextMovement(Player $player, Game $game = null)
    {
        // TODO
        return null;
    }
}
