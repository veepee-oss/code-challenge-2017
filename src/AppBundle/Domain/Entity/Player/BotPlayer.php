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
     */
    public function __construct($command, Position $position)
    {
        parent::__construct(parent::TYPE_BOT, $position);
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
        return array(
            'type' => $this->type(),
            'position' => $this->position()->serialize(),
            'command' => $this->command()
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
        return new static(
            $data['command'],
            Position::unserialize($data['position'])
        );
    }
}
