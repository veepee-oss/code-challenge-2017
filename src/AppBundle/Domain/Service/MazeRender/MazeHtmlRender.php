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
    // UTF-8 codes
    const CELL_EMPTY = '&nbsp;';
    const CELL_WALL = '&nbsp;';
    const CELL_START = '&#9678;';
    const CELL_GOAL = '&#127937;';  // &#9673;
    const CELL_PLAYER = '&#128697;';

    /**
     * Renders the game's maze with all the players
     *
     * @param Game $game
     * @return mixed
     */
    public function render(Game $game)
    {
        $maze = $game->maze();
        $players = $game->players();
        $html = '<table class="maze">';

        $rows = $maze->height();
        $cols = $maze->width();
        for ($row = 0; $row < $rows; ++$row) {
            $html .= '<tr>';
            for ($col = 0; $col < $cols; ++$col) {
                $drawPlayer = $drawShadow = false;
                foreach ($players as $player) {
                    if ($player->position()->x() == $col && $player->position()->y() == $row) {
                        $drawPlayer = true;
                        break;
                    }
                    if ($player->previous()->x() == $col && $player->previous()->y() == $row) {
                        $drawShadow = true;
                        break;
                    }
                }
                $cell = $maze[$row][$col]->getContent();
                if ($drawPlayer) {
                    $html .= '<td class="player"></td>';
                } elseif ($drawShadow) {
                    $html .= '<td class="shadow"></td>';
                } elseif ($cell == MazeCell::CELL_WALL) {
                    $html .= '<td class="wall"></td>';
                } elseif ($cell == MazeCell::CELL_START) {
                    $html .= '<td class="start"></td>';
                } elseif ($cell == MazeCell::CELL_GOAL) {
                    $html .= '<td class="goal"></td>';
                } else {
                    $html .= '<td class="empty"></td>';
                }
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }
}
