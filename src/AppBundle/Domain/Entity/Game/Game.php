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
     * @param int $status
     * @param string $uuid
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

    /**
     * Starts playing the game
     *
     * @return $this
     */
    public function startPlaying()
    {
        $this->status = static::STATUS_RUNNING;
        return $this;
    }

    /**
     * Stops playing the game
     *
     * @return $this
     */
    public function stopPlaying()
    {
        $this->status = static::STATUS_NOT_STARTED;
        return $this;
    }

    /**
     * Ends playing the game
     *
     * @return $this
     */
    public function endGame()
    {
        $this->status = static::STATUS_FINISHED;
        return $this;
    }

    /**
     * Resets the game to its initial position
     *
     * @return $this
     */
    public function resetPlaying()
    {
        $this->status = static::STATUS_NOT_STARTED;
        foreach ($this->players as $player) {
            $player->reset($this->maze()->start());
        }
        return $this;
    }
}
