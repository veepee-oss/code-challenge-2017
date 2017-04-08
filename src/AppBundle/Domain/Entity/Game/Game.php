<?php

namespace AppBundle\Domain\Entity\Game;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\Player;
use J20\Uuid\Uuid;

/**
 * Domain entity: Game
 *
 * @package AppBundle\Domain\Entity\Game
 */
class Game
{
    const STATUS_NOT_STARTED = 0;
    const STATUS_RUNNING = 1;
    const STATUS_FINISHED = 9;

    /** @var Maze */
    protected $maze;

    /** @var Player[] */
    protected $players;

    /** @var integer */
    protected $status;

    /** @var string */
    protected $uuid;

    /**
     * Game constructor.
     *
     * @param Maze $maze
     * @param Player[] $players
     */
    public function __construct(Maze $maze, array $players, $status = self::STATUS_NOT_STARTED, $uuid = null)
    {
        $this->maze = $maze;
        $this->players = $players;
        $this->status = $status;
        $this->uuid = $uuid ?: Uuid::v4();
    }

    /**
     * Get maze
     *
     * @return Maze
     */
    public function maze()
    {
        return $this->maze;
    }

    /**
     * Get Players
     *
     * @return Player[]
     */
    public function players()
    {
        return $this->players;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function status()
    {
        return $this->status;
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
}
