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
        $class = $this->getMazeGlobalCss();
        $html = '<table class="' . $class .'">';

        $rows = $maze->height();
        $cols = $maze->width();

        // For each row...
        for ($row = 0; $row < $rows; ++$row) {
            $html .= '<tr>';

            // For each column...
            for ($col = 0; $col < $cols; ++$col) {
                $class = $this->getEmptyCellCss();

                $cell = $maze[$row][$col]->getContent();
                if ($cell == MazeCell::CELL_WALL) {
                    $class = $this->getMazeWallCss();
                } elseif ($cell == MazeCell::CELL_START) {
                    $class = $this->getMazeStartCss();
                }

                foreach ($game->players() as $index => $player) {
                    if ($player->position()->x() == $col
                        && $player->position()->y() == $row) {
                        $direction = $player->direction();
                        if (!$direction) {
                            $direction = Direction::RIGHT;
                        }

                        if ($player->dead()) {
                            $class = $this->getPlayedKilledCss(1 + $index, $direction);
                        } else {
                            $class = $this->getPlayerCss(1 + $index, $direction);
                        }
                    }
                }

                foreach ($game->ghosts() as $index => $ghost) {
                    if ($ghost->position()->x() == $col
                        && $ghost->position()->y() == $row) {
                        $direction = $ghost->direction();
                        if (!$direction) {
                            $direction = Direction::RIGHT;
                        }

                        if ($ghost->isNeutralTime()) {
                            $class = $this->getGhostNeutralCss($index, $direction);
                        } elseif ($game->isKillingTime()) {
                            $class = $this->getGhostAngryCss($index, $direction);
                        } else {
                            $class = $this->getGhostCss($index, $direction);
                        }
                    }
                }

                if ($cell == MazeCell::CELL_GOAL) {
                    $class = $this->getMazeGoalCss();
                    foreach ($game->players() as $index => $player) {
                        if ($player->winner()
                            && $player->position()->x() == $col
                            && $player->position()->y() == $row) {
                            $direction = $player->direction();
                            if (!$direction) {
                                $direction = Direction::RIGHT;
                            }
                            $class = $this->getPlayerWinnerCss($index, $direction);
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

    protected function getMazeGlobalCss()
    {
        return 'x-maze';
    }

    protected function getEmptyCellCss()
    {
        return 'x-empty';
    }

    protected function getMazeWallCss()
    {
        return 'x-wall';
    }

    protected function getMazeStartCss()
    {
        return 'x-start';
    }

    protected function getMazeGoalCss()
    {
        return 'x-goal';
    }

    protected function getPlayerCss($index, $direction)
    {
        return 'x-player' . $index . '-' . $direction;
    }

    protected function getPlayedKilledCss($index, $direction)
    {
        return 'x-killed' . $index;
    }

    protected function getPlayerWinnerCss($index, $direction)
    {
        return 'x-winner';
    }

    protected function getGhostCss($index, $direction)
    {
        return 'x-ghost';
    }

    protected function getGhostNeutralCss($index, $direction)
    {
        return 'x-ghost-neutral';
    }

    protected function getGhostAngryCss($index, $direction)
    {
        return 'x-ghost-bad';
    }
}
