<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use AppBundle\Domain\Service\MovePlayer\AskPlayerNameInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use AppBundle\Domain\Service\MovePlayer\PlayerRequestInterface;

/**
 * Class BotPlayer
 *
 * @package AppBundle\Service\MovePlayer
 */
class BotPlayer implements AskPlayerNameInterface, AskNextMovementInterface
{
    /** @var PlayerRequestInterface */
    protected $playerRequest;

    /**
     * BotPlayer constructor.
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
     * @return string The player name
     * @throws MovePlayerException
     */
    public function askPlayerName(Player $player, Game $game = null)
    {
        // TODO
        return false;
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
