<?php

namespace AppBundle\Domain\Service\MazeBuilder;

use AppBundle\Domain\Entity\Maze\Maze;

/**
 * Maze builder service interface
 *
 * @package AppBundle\Domain\Service\MazeBuilder
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
