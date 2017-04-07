<?php

namespace AppBundle\Domain\Entity;

/**
 * Domain Entity: Player
 *
 * @package AppBundle\Domain\Entity
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
