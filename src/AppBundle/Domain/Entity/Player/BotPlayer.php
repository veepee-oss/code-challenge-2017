<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Position\Position;
use J20\Uuid\Uuid;

/**
 * Domain entity; BotPlayer
 *
 * @package AppBundle\Domain\Entity\Player
 */
class BotPlayer extends Player
{
    /** @var string */
    protected $command;

    /**
     * BotPlayer constructor.
     *
     * @param string $command
     * @param Position $position
     * @param Position $previous
     * @param int $status
     * @param \DateTime $timestamp
     * @param string $uuid
     * @param string $name
     * @param string $email
     */
    public function __construct(
        $command,
        Position $position,
        Position $previous = null,
        $status = null,
        \DateTime $timestamp = null,
        $uuid = null,
        $name = null,
        $email = null
    ) {
        parent::__construct(parent::TYPE_BOT, $position, $previous, $status, $timestamp, $uuid, $name, $email);
        $this->command = $command;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function command()
    {
        return $this->command;
    }

    /**
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return parent::serialize() + array(
            'command' => $this->command()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return BotPlayer
     */
    public static function unserialize(array $data)
    {
        return new static(
            $data['command'],
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
