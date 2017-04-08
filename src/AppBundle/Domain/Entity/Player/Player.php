<?php

namespace AppBundle\Domain\Entity\Player;

/**
 * Domain Entity: Player
 *
 * @package AppBundle\Domain\Entity\Player
 */
interface Player
{
    const TYPE_API = 1;
    const TYPE_BOT = 2;

    /**
     * Get type
     *
     * @return int
     */
    public function type();

    /**
     * Get execution data (url, command, ...)
     *
     * @return string
     */
    public function execData();
}
