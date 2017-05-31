<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class PlayerRequestInterface
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
interface PlayerRequestInterface
{
    const DEFAULT_VIEW_RANGE = 4;

    /**
     * Creates the request data to send to the player bot or api. The request data will be a json object.
     *
     * {
     *     "game": {
     *         "id": "uuid"
     *     },
     *     "player": {
     *         "id": "uuid",
     *         "name": "string",
     *         "position": {
     *             "y": "int",
     *             "x": "int"
     *         },
     *         "previous": {
     *             "y": "int",
     *             "x": "int"
     *         },
     *         "area": {
     *             "y1": "int",
     *             "x1": "int",
     *             "y2": "int",
     *             "x2": "int"
     *         }
     *     },
     *     "maze": {
     *         "size": {
     *             "height": "int",
     *             "width": "int"
     *         },
     *         "goal": {
     *             "y": "int",
     *             "x": "int"
     *         },
     *         "walls": [
     *             {
     *                 "y": "int",
     *                 "x": "int"
     *             }
     *         ]
     *     },
     *     "ghosts": [
     *         {
     *             "y": "int",
     *             "x": "int"
     *         }
     *     ]
     * }
     *
     * @param Player $player    The player data.
     * @param Game   $game      The game data.
     * @param int    $viewRange The view distance.
     * @return string Request in json format
     */
    public function create(Player $player, Game $game, $viewRange = self::DEFAULT_VIEW_RANGE);
}
