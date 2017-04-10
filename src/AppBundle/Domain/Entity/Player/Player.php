<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Position\Position;

/**
 * Domain Entity: Player
 *
 * @package AppBundle\Domain\Entity\Player
 */
class Player
{
    const TYPE_API = 1;
    const TYPE_BOT = 2;

    const DIRECTION_UP = 'up';
    const DIRECTION_DOWN = 'down';
    const DIRECTION_LEFT = 'left';
    const DIRECTION_RIGHT = 'right';

    /** @var int */
    protected $type;

    /** @var Position */
    protected $position;

    /**
     * Player constructor.
     *
     * @param int $type
     * @param Position $position
     */
    public function __construct($type, Position $position)
    {
        $this->type = $type;
        $this->position = $position;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Get position
     *
     * @return Position
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * Moves the player
     *
     * @param string $direction
     * @return Position
     */
    public function move($direction)
    {
        $y = $this->position->y();
        $x = $this->position->x();
        switch ($direction) {
            case static::DIRECTION_UP:
                $y--;
                break;

            case static::DIRECTION_DOWN:
                $y++;
                break;

            case static::DIRECTION_LEFT:
                $x--;
                break;

            case static::DIRECTION_RIGHT:
                $y--;
                break;
        }

        $this->position = new Position($y, $x);

        return $this->position();
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'type' => $this->type(),
            'position' => $this->position()->serialize()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Player
     */
    public static function unserialize(array $data)
    {
        return new static(
            $data['type'],
            Position::unserialize($data['position'])
        );
    }
}
