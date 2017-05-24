<?php

namespace AppBundle\Domain\Entity\Ghost;

use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class Ghost
 *
 * @package AppBundle\Domain\Entity\Ghost
 */
class Ghost extends MazeObject
{
    /** Ghost types */
    const TYPE_SIMPLE = 1;

    /** @var int */
    protected $type;

    /** Default neutral time */
    const DEFAULT_NEUTRAL_TIME = 5;

    /** @var int */
    protected $neutralTime;

    /**
     * Ghost constructor.
     *
     * @param int $type
     * @param Position $position
     * @param Position $previous
     * @param int      $neutralTime
     */
    public function __construct(
        $type,
        Position $position,
        Position $previous = null,
        $neutralTime = 0
    ) {
        parent::__construct($position, $previous);
        $this->type = $type;
        $this->neutralTime = $neutralTime;
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
     * Get grace time
     *
     * @return int
     */
    public function isNeutralTime()
    {
        return $this->neutralTime < static::DEFAULT_NEUTRAL_TIME;
    }

    /**
     * Moves the player
     *
     * @param Position $position
     * @return $this
     */
    public function move(Position $position)
    {
        parent::move($position);
        if ($this->isNeutralTime()) {
            $this->neutralTime++;
        }

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
            'neutralTime' => $this->neutralTime
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Ghost
     */
    public static function unserialize(array $data)
    {
        return new static(
            $data['type'],
            Position::unserialize($data['position']),
            Position::unserialize(isset($data['previous']) ? $data['previous'] : $data['position']),
            isset($data['neutralTime']) ? $data['neutralTime'] : 0
        );
    }
}
