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

    /**
     * Ghost constructor.
     *
     * @param int $type
     * @param Position $position
     * @param Position $previous
     */
    public function __construct($type, Position $position, Position $previous = null)
    {
        parent::__construct($position, $previous);
        $this->type = $type;
    }

    /**
     * Get type
     * @return int
     */
    public function type()
    {
        return $this->type;
    }
}
