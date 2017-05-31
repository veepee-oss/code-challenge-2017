<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class PlayerRequest
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class PlayerRequest implements PlayerRequestInterface
{
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
    public function create(Player $player, Game $game, $viewRange = self::DEFAULT_VIEW_RANGE)
    {
        $maze = $game->maze();
        $height = $maze->height();
        $width = $maze->width();
        $pos = $player->position();
        $prev = $player->previous();

        $size = 1 + ($viewRange * 2);
        while ($size > $height || $size > $height) {
            --$viewRange;
            $size = 1 + ($viewRange * 2);
        }

        $y1 = $pos->y() - $viewRange;
        $y2 = $pos->y() + $viewRange;
        $x1 = $pos->x() - $viewRange;
        $x2 = $pos->x() + $viewRange;

        if ($y1 < 0) {
//            $y2 -= $y1;
            $y1 = 0;
        } elseif ($y2 >= $height) {
//            $y1 -= ($pos->y() - $height + 1);
            $y2 = $height - 1;
        }

        if ($x1 < 0) {
//            $x2 -= $x1;
            $x1 = 0;
        } elseif ($x2 >= $width) {
//            $x1 -= ($pos->x() - $width + 1);
            $x2 = $width - 1;
        }

        $walls = array();
        for ($y = $y1; $y <= $y2; ++$y) {
            for ($x = $x1; $x <= $x2; ++$x) {
                if ($maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
                    $walls[] = array(
                        'y' => $y,
                        'x' => $x
                    );
                }
            }
        }

        $ghosts = array();
        foreach ($game->ghosts() as $ghost) {
            $ghostPos = $ghost->position();
            if ($ghostPos->y() >= $y1
                && $ghostPos->y() <= $y2
                && $ghostPos->x() >= $x1
                && $ghostPos->x() <= $x2) {
                $ghosts[] = array(
                    'y' => $ghostPos->y(),
                    'x' => $ghostPos->x()
                );
            }
        }

        $data = array(
            'game'      => array(
                'id'        => $player->uuid()
            ),
            'player'    => array(
                'id'        => $player->uuid(),
                'name'      => $player->name(),
                'position'  => array(
                    'y'         => $pos->y(),
                    'x'         => $pos->x()
                ),
                'previous'  => array(
                    'y'         => $prev->y(),
                    'x'         => $prev->x()
                ),
                'area'      => array(
                    'y1'        => $y1,
                    'x1'        => $x1,
                    'y2'        => $y2,
                    'x2'        => $x2
                )
            ),
            'maze'      => array(
                'size'      => array(
                    'height'    => $height,
                    'width'     => $width
                ),
                'goal'  => array(
                    'y'         => $maze->goal()->y(),
                    'x'         => $maze->goal()->x()
                ),
                'walls'     => $walls
            ),
            'ghosts'    => $ghosts
        );

        return json_encode($data);
    }
}
