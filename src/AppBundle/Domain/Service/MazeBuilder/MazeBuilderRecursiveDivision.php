<?php

namespace AppBundle\Domain\Service\MazeBuilder;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Maze builder using recursive division method
 *
 * @package AppBundle\Domain\Service\MazeBuilder
 */
class MazeBuilderRecursiveDivision implements MazeBuilderInterface
{
    /** @var \AppBundle\Domain\Entity\Maze\Maze */
    protected $maze = null;

    /** Constants */
    const HORIZONTAL = 1;
    const VERTICAL = 2;

    /**
     * Creates a random maze
     *
     * @param int $height
     * @param int $width
     * @return Maze
     * @throws MazeBuilderException
     */
    public function buildRandomMaze($height, $width)
    {
        $this
            ->createMaze($height, $width)
            ->createBorders()
            ->makeDivisions(0, 0, $width - 1, $height - 1)
            ->createStartAndGoal();

        return $this->maze;
    }

    /**
     * Creates the empty maze object
     *
     * @param int $height
     * @param int $width
     * @return $this
     */
    protected function createMaze($height, $width)
    {
        $this->maze = new Maze($height, $width);
        return $this;
    }

    /**
     * Creates the borders of the maze
     *
     * @return $this
     */
    protected function createBorders()
    {
        $height = $this->maze->height();
        $width = $this->maze->width();

        $x1 = 0;
        $y1 = 0;
        $x2 = $width - 1;
        $y2 = $height - 1;

        $this->drawVerticalWall($x1, $y1, $y2);
        $this->drawVerticalWall($x2, $y1, $y2);

        $this->drawHorizontalWall($y1, $x1, $x2);
        $this->drawHorizontalWall($y2, $x1, $x2);

        return $this;
    }

    /**
     * Makes the divisions of the maze
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return $this
     */
    protected function makeDivisions($x1, $y1, $x2, $y2, $orientation = null)
    {
        $width = $x2 - $x1 + 1;
        $height = $y2 - $y1 + 1;
        if ($width < 5|| $height < 5) {
            return;
        }

        $px = rand($x1 + 2, $x2 - 2);
        $py = rand($y1 + 2, $y2 - 2);

        $orientation = $orientation ?: $this->chooseOrientation($width, $height);
        if (self::HORIZONTAL == $orientation) {
            $this->drawHorizontalWall($py, $x1, $x2);
            $this->maze[$py][$px]->setContent(MazeCell::CELL_EMPTY);
            $orientation = self::VERTICAL;
        } else {
            $this->drawVerticalWall($px, $y1, $y2);
            $this->maze[$py][$px]->setContent(MazeCell::CELL_EMPTY);
            $orientation = self::HORIZONTAL;
        }

        $this->makeDivisions($x1, $y1, $px, $py, $orientation);
        $this->makeDivisions($x1, $py, $px, $y2, $orientation);
        $this->makeDivisions($px, $y1, $x2, $py, $orientation);
        $this->makeDivisions($px, $py, $x2, $y2, $orientation);

        return $this;
    }

    /**
     * Creates the goal in a random wall
     *
     * @return $this
     */
    protected function createStartAndGoal()
    {
        $width = $this->maze->width();
        $height = $this->maze->height();

        $wall = rand(0, 3);
        switch ($wall) {
            case 0:
            case 1:
                $x = rand(1, $width - 2);
                if ($wall == 0) {
                    // Start on top wall
                    $this->maze->setStart(new Position(1, $width - $x - 1));
                    $this->maze[1][$width - $x - 1]->setContent(MazeCell::CELL_START);

                    // Goal on bottom wall
                    $this->maze->setGoal(new Position($height - 1, $x));
                    $this->maze[$height - 1][$x]->setContent(MazeCell::CELL_GOAL);
                    $this->maze[$height - 2][$x]->setContent(MazeCell::CELL_EMPTY);
                } else {
                    // Start on bottom wall
                    $this->maze->setStart(new Position($height - 2, $width - $x - 1));
                    $this->maze[$height - 2][$width - $x - 1]->setContent(MazeCell::CELL_START);

                    // Goal on top wall
                    $this->maze->setGoal(new Position(0, $x));
                    $this->maze[0][$x]->setContent(MazeCell::CELL_GOAL);
                    $this->maze[1][$x]->setContent(MazeCell::CELL_EMPTY);
                }
                break;

            case 2:
            case 3:
                $y = rand(1, $height - 2);
                if ($wall == 2) {
                    // Start on left wall
                    $this->maze->setStart(new Position($height - $y - 1, 1));
                    $this->maze[$height - $y - 1][1]->setContent(MazeCell::CELL_START);

                    // Goal on right wall
                    $this->maze->setGoal(new Position($y, $width - 1));
                    $this->maze[$y][$width - 1]->setContent(MazeCell::CELL_GOAL);
                    $this->maze[$y][$width - 2]->setContent(MazeCell::CELL_EMPTY);
                } else {
                    // Start on right wall
                    $this->maze->setStart(new Position($height - $y - 1, $width - 2));
                    $this->maze[$height - $y -1][$width - 2]->setContent(MazeCell::CELL_START);

                    // Goal on left wall
                    $this->maze->setGoal(new Position($y, 0));
                    $this->maze[$y][0]->setContent(MazeCell::CELL_GOAL);
                    $this->maze[$y][1]->setContent(MazeCell::CELL_EMPTY);
                }
                break;
        }
        return $this;
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
    protected function drawVerticalWall($x, $y1, $y2, $wall = MazeCell::CELL_WALL)
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
    protected function drawHorizontalWall($y, $x1, $x2, $wall = MazeCell::CELL_WALL)
    {
        for ($i = $x1; $i <= $x2; $i++) {
            $this->maze[$y][$i]->setContent($wall);
        }
    }
}
