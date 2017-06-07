<?php

namespace AppBundle\Domain\Entity\Position;

/**
 * Class Position
 *
 * @package AppBundle\Domain\Entity\Player
 */
class Position
{
    /** @var int */
    protected $y;

    /** @var int */
    protected $x;

    /**
     * Position constructor.
     *
     * @param int $y
     * @param int $x
     */
    public function __construct($y, $x)
    {
        $this->y = $y;
        $this->x = $x;
    }

    /**
     * Get Y
     *
     * @return int
     */
    public function y()
    {
        return $this->y;
    }

    /**
     * Get X
     *
     * @return int
     */
    public function x()
    {
        return $this->x;
    }

    /**
     * Moves a position in a direction
     *
     * @param string $dir
     * @return $this
     */
    public function moveTo($dir)
    {
        switch ($dir) {
            case Direction::UP:
                --$this->y;
                break;

            case Direction::DOWN:
                ++$this->y;
                break;

            case Direction::LEFT:
                --$this->x;
                break;

            case Direction::RIGHT:
                ++$this->x;
                break;
        }
        return $this;
    }

    /**
     * Moves a position in a direction, returning a new object.
     *
     * @param Position $pos
     * @param int $dir
     * @return Position
     */
    public static function move(Position $pos, $dir)
    {
        $new = clone $pos;
        return $new->moveTo($dir);
    }

    /**
     * Return if tis the same position
     *
     * @param Position $pos
     * @return bool
     */
    public function equals(Position $pos)
    {
        return ($this->y() == $pos->y()
            && $this->x() == $pos->x());
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'y' => $this->y(),
            'x' => $this->x()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Position
     */
    public static function unserialize(array $data)
    {
        return new static($data['y'], $data['x']);
    }
}
