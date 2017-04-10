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

    /** @var Position */
    protected $previous;

    /**
     * Player constructor.
     *
     * @param int $type
     * @param Position $position
     * @param Position $previous
     */
    public function __construct($type, Position $position, Position $previous = null)
    {
        $this->type = $type;
        $this->position = $position;
        $this->previous = $previous ?: $position;
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
     * Get previous position
     *
     * @return Position
     */
    public function previous()
    {
        return $this->previous;
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

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'type' => $this->type(),
            'position' => $this->position()->serialize(),
            'previous' => $this->previous()->serialize(),
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
            Position::unserialize($data['position']),
            Position::unserialize(isset($data['previuos']) ? $data['previuos'] : $data['position'])
        );
    }
}
