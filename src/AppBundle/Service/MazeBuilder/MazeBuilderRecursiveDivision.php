<?php

namespace AppBundle\Service\MazeBuilder;

use AppBundle\Entity\Maze;
use AppBundle\Entity\MazeCell;

/**
 * Maze builder using recursive division method
 *
 * @package AppBundle\Service\MazeBuilder
 */
class MazeBuilderRecursiveDivision implements MazeBuilderInterface
{
    /** @var Maze */
    protected $maze = null;

    /** Constants */
    const HORIZONTAL = 0;
    const VERTICAL = 1;

    /**
     * Creates a random maze
     *
     * @param int $width
     * @param int $height
     * @return Maze
     * @throws MazeBuilderException
     */
    public function buildRandomMaze($width, $height)
    {
        $this->createMaze($width, $height);

        $this->createBorders();

        $this->makeDivisions(0, 0, $width - 1, $height - 1);

        return $this->maze;
    }

    /**
     * Creates the empty maze object
     *
     * @param int $width
     * @param int $height
     * @return void
     */
    protected function createMaze($width, $height)
    {
        $this->maze = new Maze($width, $height);
    }

    /**
     * Creates the borders of the maze
     *
     * @return void
     */
    protected function createBorders()
    {
        $width = $this->maze->getWidth();
        $height = $this->maze->getHeight();

        $x1 = 0;
        $y1 = 0;
        $x2 = $width - 1;
        $y2 = $height - 1;

        $this->drawVerticalWall($x1, $y1, $y2);
        $this->drawVerticalWall($x2, $y1, $y2);

        $this->drawHorizontalWall($y1, $x1, $x2);
        $this->drawHorizontalWall($y2, $x1, $x2);
    }

    /**
     * Makes the divisions of the maze
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return void
     */
    protected function makeDivisions($x1, $y1, $x2, $y2)
    {
        $width = $x2 - $x1 + 1;
        $height = $y2 - $y1 + 1;
        if ($width <= 2 || $height <= 2) {
            return;
        }

        $px = rand($x1 + 1, $x2 - 1);
        $py = rand($y1 + 1, $y2 - 1);

        $orientation = $this->chooseOrientation($width, $height);
        if (self::HORIZONTAL == $orientation) {
            $this->drawHorizontalWall($py, $x1, $x2);
            $this->maze[$py][$px]->setContent(MazeCell::EMPTY_CELL);
        } else {
            $this->drawVerticalWall($px, $y1, $y2);
            $this->maze[$py][$px]->setContent(MazeCell::EMPTY_CELL);
        }

        $this->makeDivisions($x1, $y1, $px, $py);
//        $this->makeDivisions($x1, $py, $px, $y2);P
//        $this->makeDivisions($px, $y1, $x2, $py);
        $this->makeDivisions($px, $py, $x2, $x2);
    }

    /**
     * Chooses the orientation of a new division wall (vertical or horizontal)
     *
     * @param int $width
     * @param int $height
     * @return int
     */
    protected function chooseOrientation($width, $height)
    {
        if ($width < $height) {
            return self::HORIZONTAL;
        } elseif ($height < $width) {
            return self::VERTICAL;
        } else {
            return (0 == rand(0, 1)) ? self::HORIZONTAL : self::VERTICAL;
        }
    }

    /**
     * Draws a vertical wall between two points
     *
     * @param int $x
     * @param int $y1
     * @param int $y2
     * @param int $wall
     */
    protected function drawVerticalWall($x, $y1, $y2, $wall = MazeCell::WALL)
    {
        for ($i = $y1; $i <= $y2; $i++) {
            $this->maze[$i][$x]->setContent($wall);
        }
    }

    /**
     * Draws an horizontal wall between two points
     *
     * @param int $y
     * @param int $x1
     * @param int $x2
     * @param int $wall
     */
    protected function drawHorizontalWall($y, $x1, $x2, $wall = MazeCell::WALL)
    {
        for ($i = $x1; $i <= $x2; $i++) {
            $this->maze[$y][$i]->setContent($wall);
        }
    }
}
