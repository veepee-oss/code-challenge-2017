<?php

namespace AppBundle\Service\MazeBuilder;

use AppBundle\Entity\Maze;

/**
 * Maze builder service interface
 *
 * @package AppBundle\Service\MazeBuilder
 */
interface MazeBuilderInterface
{
    /**
     * Creates a random maze
     *
     * @param int $width
     * @param int $height
     * @return Maze
     * @throws MazeBuilderException
     */
    public function buildRandomMaze($width, $height);
}
