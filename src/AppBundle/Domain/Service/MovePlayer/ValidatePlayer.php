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
        /** @var AskPlayerNameInterface $playerService */
        $playerService = $this->playerServiceFactory->locate($player);

        try {
            // Asks for the name and email of the player
            $data = $playerService->askPlayerName($player, $game);
            if (!$data) {
                return false;
            }
        } catch (MovePlayerException $exc) {
            return false;
        }

        $player->setPlayerIds($data['name'], $data['email']);
        return true;
    }
}
