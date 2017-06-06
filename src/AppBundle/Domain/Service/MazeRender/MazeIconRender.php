<?php

namespace AppBundle\Domain\Service\MazeRender;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Position\Direction;

/**
 * Class MazeIconRender
 *
 * @package AppBundle\Domain\Service\MazeRender
 */
class MazeIconRender implements MazeRenderInterface
{
    /**
     * Renders the game's maze with all the players
     *
     * @param Game $game
     * @return mixed
     */
    public function render(Game $game)
    {
        $maze = $game->maze();
        $html = '<table class="x-maze">';

        $rows = $maze->height();
        $cols = $maze->width();

        // For each row...
        for ($row = 0; $row < $rows; ++$row) {
            $html .= '<tr>';

            // For each column...
            for ($col = 0; $col < $cols; ++$col) {
                $class = 'x-empty';

                $cell = $maze[$row][$col]->getContent();
                if ($cell == MazeCell::CELL_WALL) {
                    $class = 'x-wall';
                } elseif ($cell == MazeCell::CELL_START) {
                    $class = 'x-start';
                }

                foreach ($game->players() as $index => $player) {
                    if ($player->position()->x() == $col
                        && $player->position()->y() == $row) {
                        if ($player->dead()) {
                            $class = 'x-killed' . (1 + $index);
                        } else {
                            $direction = $player->direction();
                            if (!$direction) {
                                $direction = Direction::RIGHT;
                            }
                            $class = 'x-player' . (1 + $index) . '-' . $direction;
                        }
                    }
                }

                foreach ($game->ghosts() as $index => $ghost) {
                    if ($ghost->position()->x() == $col
                        && $ghost->position()->y() == $row) {
                        if ($ghost->isNeutralTime()) {
                            $class = 'x-ghost-neutral';
                        } elseif ($game->isKillingTime()) {
                            $class = 'x-ghost-bad';
                        } else {
                            $class = 'x-ghost';
                        }
                    }
                }

                if ($cell == MazeCell::CELL_GOAL) {
                    $class = 'x-goal';
                    foreach ($game->players() as $player) {
                        if ($player->winner()
                            && $player->position()->x() == $col
                            && $player->position()->y() == $row) {
                            $class = 'x-winner';
                            break;
                        }
                    }
                }

                $html .= '<td class="' . $class . '"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
}
