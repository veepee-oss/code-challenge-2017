<?php

namespace AppBundle\Domain\Service\MazeRender;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\MazeCell;

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
                $cell = $maze[$row][$col]->getContent();
                if ($cell == MazeCell::CELL_WALL) {
                    $html .= '<td class="x-wall"></td>';
                } elseif ($cell == MazeCell::CELL_START) {
                    $html .= '<td class="x-start"></td>';
                } elseif ($cell == MazeCell::CELL_GOAL) {
                    $drawWinner = false;
                    $players = $game->players();
                    foreach ($players as $index => $player) {
                        if ($player->winner()
                            && $player->position()->x() == $col && $player->position()->y() == $row) {
                            $drawWinner = true;
                        }
                    }

                    if (null != $drawWinner) {
                        $html .= '<td class="x-winner"></td>';
                    } else {
                        $html .= '<td class="x-goal"></td>';
                    }
                } else {
                    $direction = null;
                    $drawPlayer = null;
                    $drawKilled = null;
                    $drawGhost = false;

                    $players = $game->players();
                    foreach ($players as $index => $player) {
                        if ($player->position()->x() == $col && $player->position()->y() == $row) {
                            if ($player->dead()) {
                                $drawKilled = 1 + $index;
                            } else {
                                $drawPlayer = 1 + $index;
                                $direction = $player->direction();
                            }
                        }
                    }

                    $ghosts = $game->ghosts();
                    foreach ($ghosts as $index => $ghost) {
                        if ($ghost->position()->x() == $col && $ghost->position()->y() == $row) {
                            $drawGhost = true;
                        }
                    }

                    if (null != $drawPlayer) {
                        $html .= '<td class="x-player' . $drawPlayer . '-' . $direction . '"></td>';
                    } elseif ($drawKilled) {
                        $html .= '<td class="x-killed' . $drawKilled . '"></td>';
                    } elseif ($drawGhost) {
                        $html .= '<td class="x-ghost"></td>';
                    } else {
                        $html .= '<td class="x-empty"></td>';
                    }
                }
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
}
