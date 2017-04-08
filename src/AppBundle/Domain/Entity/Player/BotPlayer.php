<?php

namespace AppBundle\Domain\Entity\Player;

/**
 * Domain entity; BotPlayer
 *
 * @package AppBundle\Domain\Entity\Player
 */
class BotPlayer implements Player
{
    /** @var string */
    protected $command;

    /**
     * BotPlayer constructor.
     *
     * @param string $command
     */
    public function __construct($command)
    {
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
     * Get type
     *
     * @return int
     */
    public function type()
    {
        return self::TYPE_BOT;
    }

    /**
     * Get execution data (url, command, ...)
     *
     * @return string
     */
    public function execData()
    {
        return $this->command();
    }
}
