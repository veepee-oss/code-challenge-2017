<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Position\Position;
use J20\Uuid\Uuid;

/**
 * Domain Entity: Player
 *
 * @package AppBundle\Domain\Entity\Player
 */
class Player extends MazeObject
{
    /** Player types */
    const TYPE_API = 1;
    const TYPE_BOT = 2;

    /** Player statuses */
    const STATUS_PLAYING = 1;
    const STATUS_DIED = 8;
    const STATUS_WINNER = 12;

    /** @var int */
    protected $type;

    /** @var int */
    protected $status;

    /** @var \DateTime */
    protected $timestamp;

    /** @var string */
    protected $uuid;

    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /**
     * Player constructor.
     *
     * @param int $type
     * @param Position $position
     * @param Position $previous
     * @param int $status
     * @param \DateTime $timestamp
     * @param string $uuid
     * @param string $name
     * @param string $email
     */
    public function __construct(
        $type,
        Position $position,
        Position $previous = null,
        $status = null,
        \DateTime $timestamp = null,
        $uuid = null,
        $name = null,
        $email = null
    ) {
        parent::__construct($position, $previous);
        $this->type = $type;
        $this->status = $status ?: static::STATUS_PLAYING;
        $this->timestamp = $timestamp ?: new \DateTime();
        $this->uuid = $uuid ?: Uuid::v4();
        $this->name = $name ?: $this->uuid;
        $this->email = $email;
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
     * Get current status
     *
     * @return int
     */
    public function status()
    {
        return $this->status;
    }

    /**
     * Get current timestamp
     *
     * @return \DateTime
     */
    public function timestamp()
    {
        return $this->timestamp;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function uuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * Sets the name of the player
     *
     * @param string $name
     * @param string $email
     * @return $this
     */
    public function setPlayerIds($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
        return $this;
    }

    /**
     * The player wins the game
     *
     * @return $this
     */
    public function wins()
    {
        $this->status = static::STATUS_WINNER;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Get if the player won the game
     *
     * @return bool
     */
    public function winner()
    {
        return static::STATUS_WINNER == $this->status;
    }

    /**
     * The player dies
     *
     * @return $this
     */
    public function dies()
    {
        $this->status = static::STATUS_DIED;
        $this->timestamp = new \DateTime();
        return $this;
    }

    /**
     * Get if the player died
     *
     * @return bool
     */
    public function dead()
    {
        return static::STATUS_DIED == $this->status;
    }

    /**
     * Reset the game for this player
     *
     * @param Position $pos
     * @return $this
     */
    public function reset(Position $pos)
    {
        $this->status = static::STATUS_PLAYING;
        $this->timestamp = new \DateTime();
        $this->position = clone $pos;
        $this->previous = clone $pos;
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
            'status' => $this->status(),
            'timestamp' => $this->timestamp()->format('YmdHisu'),
            'uuid' => $this->uuid(),
            'name' => $this->name(),
            'email' => $this->email()
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
            isset($data['previous']) ? Position::unserialize($data['previous']) : null,
            isset($data['status']) ? $data['status'] : null,
            isset($data['timestamp']) ? \DateTime::createFromFormat('YmdHisu', $data['timestamp']) : null,
            isset($data['uuid']) ? $data['uuid'] : null,
            isset($data['name']) ? $data['name'] : null,
            isset($data['email']) ? $data['email'] : null
        );
    }
}
