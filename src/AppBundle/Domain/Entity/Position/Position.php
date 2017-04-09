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
