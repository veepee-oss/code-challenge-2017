<?php

namespace AppBundle\Domain\Entity;

/**
 * Domain Entity MazeCell
 *
 * @package AppBundle\Domain\Entity
 */
class MazeCell
{
    const CELL_EMPTY = 0x00;
    const CELL_WALL = 0x80;
    const CELL_GOAL = 0xFF;

    /** @var int */
    protected $content;

    /**
     * MazeCell constructor.
     *
     * @param int $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param int $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}
