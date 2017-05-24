<?php

namespace AppBundle\Domain\Service\MazeRender;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\MazeCell;

/**
 * Class MazeHtmlRender
 *
 * @package AppBundle\Domain\Service\MazeRender
 */
class MazeHtmlRender implements MazeRenderInterface
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
        $html = '<table class="maze">';

        $rows = $maze->height();
        $cols = $maze->width();

        // For each row...
        for ($row = 0; $row < $rows; ++$row) {
            $html .= '<tr>';

            // For each column...
            for ($col = 0; $col < $cols; ++$col) {
                $cell = $maze[$row][$col]->getContent();
                if ($cell == MazeCell::CELL_WALL) {
                    $html .= '<td class="wall"></td>';
                } elseif ($cell == MazeCell::CELL_START) {
                    $html .= '<td class="start"></td>';
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
                        $html .= '<td class="winner"></td>';
                    } else {
                        $html .= '<td class="goal"></td>';
                    }
                } else {
                    $drawPlayer = null;
                    $drawKilled = false;
                    $drawGhost = false;
                    $drawNeutral = false;

                    $players = $game->players();
                    foreach ($players as $index => $player) {
                        if ($player->position()->x() == $col && $player->position()->y() == $row) {
                            if ($player->dead()) {
                                $drawKilled = true;
                            } else {
                                $drawPlayer = 1 + $index;
                            }
                        }
                    }

                    $ghosts = $game->ghosts();
                    foreach ($ghosts as $index => $ghost) {
                        if ($ghost->position()->x() == $col && $ghost->position()->y() == $row) {
                            if ($ghost->isNeutralTime()) {
                                $drawNeutral = true;
                            } else {
                                $drawGhost = true;
                            }
                        }
                    }

                    if (null != $drawPlayer) {
                        $html .= '<td class="player' . $drawPlayer . '"></td>';
                    } elseif ($drawKilled) {
                        $html .= '<td class="killed"></td>';
                    } elseif ($drawGhost) {
                        $html .= '<td class="ghost"></td>';
                    } elseif ($drawNeutral) {
                        $html .= '<td class="ghost-neutral"></td>';
                    } else {
                        $html .= '<td class="empty"></td>';
                    }
                }
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
}
