<?php

namespace AppBundle\Domain\Service\MazeRender;

use AppBundle\Domain\Entity\Game\Game;

/**
 * Class MazeRenderInterface
 *
 * @package AppBundle\Domain\Service\MazeRender
 */
interface MazeRenderInterface
{
    /**
     * Renders the game's maze with all the playyers
     *
     * @param Game $game
     * @return mixed
     */
    public function render(Game $game);
}
