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

    const STATUS_PLAYING = 1;
    const STATUS_DIED = 8;
    const STATUS_WINNER = 12;

    /** @var int */
    protected $type;

    /** @var Position */
    protected $position;

    /** @var Position */
    protected $previous;

    /** @var int */
    protected $status;

    /**
     * Player constructor.
     *
     * @param int $type
     * @param Position $position
     * @param Position $previous
     * @param int $status
     */
    public function __construct($type, Position $position, Position $previous = null, $status = null)
    {
        $this->type = $type;
        $this->position = $position;
        $this->previous = $previous ?: $position;
        $this->status = $status ?: static::STATUS_PLAYING;
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
     * Get current status
     *
     * @return int
     */
    public function status()
    {
        return $this->status;
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
     * Win the game
     *
     * @return $this
     */
    public function winner()
    {
        $this->status = static::STATUS_WINNER;
        return $this;
    }

    /**
     * Loose the game
     *
     * @return $this
     */
    public function looser()
    {
        $this->status = static::STATUS_DIED;
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
            'status' => $this->status()
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
            Position::unserialize(isset($data['previuos']) ? $data['previuos'] : $data['position']),
            isset($data['status']) ? $data['status'] : static::STATUS_PLAYING
        );
    }
}
