<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class ValidatePlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class ValidatePlayer implements ValidatePlayerInterface
{
    /** @var MovePlayerFactory */
    protected $playerServiceFactory;

    /**
     * MovePlayer constructor.
     *
     * @param MovePlayerFactory $playerServiceFactory
     */
    public function __construct(MovePlayerFactory $playerServiceFactory)
    {
        $this->playerServiceFactory = $playerServiceFactory;
    }

    /**
     * Validates the player asking for the name
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=success, false=error
     * @throws MovePlayerException
     */
    public function validatePlayer(Player& $player, Game $game = null)
    {
        try {
            /** @var AskPlayerNameInterface $playerService */
            $playerService = $this->playerServiceFactory->locate($player);

            // Reads the next movement of the player: "up", "down", "left" or "right".
            $name = $playerService->askPlayerName($player, $game);
        } catch (MovePlayerException $exc) {
            return false;
        }
        if (!$name) {
            return false;
        }

        $player->setName($name);
        return true;
    }
}
