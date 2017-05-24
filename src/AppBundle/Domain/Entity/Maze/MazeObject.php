<?php

namespace AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Position\Position;

/**
 * Class MazeObject
 *
 * @package AppBundle\Domain\Entity\Maze
 */
class MazeObject
{
    /** Directions */
    const DIRECTION_STOP = '';
    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';
    const DIRECTION_LEFT = 'left';
    const DIRECTION_RIGHT = 'right';

    /** @var Position */
    protected $position;

    /** @var Position */
    protected $previous;

    /**
     * MazeObject constructor.
     *
     * @param Position $position
     * @param Position $previous
     */
    public function __construct(Position $position, Position $previous = null)
    {
        $this->position = $position;
        $this->previous = $previous ?: $position;
    }

    /**
     * Get current position
     *
     * @return Position
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * Get previous position
     *
     * @return Position
     */
    public function previous()
    {
        return $this->previous;
    }

    /**
     * Get current direction
     *
     * @return string
     */
    public function direction()
    {
        if (null == $this->position || null == $this->previous) {
            return static::DIRECTION_STOP;
        }

        if ($this->position->y() < $this->previous->y()) {
            return static::DIRECTION_UP;
        }

        if ($this->position->y() > $this->previous->y()) {
            return static::DIRECTION_DOWN;
        }

        if ($this->position->x() < $this->previous->x()) {
            return static::DIRECTION_LEFT;
        }

        if ($this->position->x() > $this->previous->x()) {
            return static::DIRECTION_RIGHT;
        }

        return static::DIRECTION_STOP;
    }

    /**
     * Moves the player
     *
     * @param Position $position
     * @return $this
     */
    public function move(Position $position)
    {
        $this->previous = clone $this->position;
        $this->position = clone $position;
        return $this;
    }
}
