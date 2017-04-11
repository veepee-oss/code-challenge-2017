<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Position\Position;

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
     */
    public function __construct($command, Position $position, Position $previous = null, $status = null)
    {
        parent::__construct(parent::TYPE_BOT, $position, $previous, $status);
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
            Position::unserialize(isset($data['previuos']) ? $data['previuos'] : $data['position']),
            isset($data['status']) ? $data['status'] : static::STATUS_PLAYING
        );
    }
}
