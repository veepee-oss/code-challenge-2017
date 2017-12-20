<?php

namespace AppBundle\Domain\Service\MazeRender;

/**
 * Class MazeXmasIconRender
 *
 * @package AppBundle\Domain\Service\MazeRender
 */
class MazeXmasIconRender extends MazeIconRender
{
    protected function getMazeGlobalCss()
    {
        return 'x-maze x-mas';
    }

    protected function getEmptyCellCss()
    {
        return 'x-empty';
    }

    protected function getMazeWallCss()
    {
        return 'x-mas-wall';
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
        return 'x-player' . $index;
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
        return 'x-ghost-regular';
    }

    protected function getGhostNeutralCss($index, $direction)
    {
        return 'x-ghost-neutral';
    }

    protected function getGhostAngryCss($index, $direction)
    {
        return 'x-ghost-angry';
    }
}
