<?php

namespace AppBundle\Form\CreateGame;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateGameEntity
 *
 * @package AppBundle\Form\CreateGame
 */
class GameEntity
{
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=10, max=1000)
     */
    private $height = 40;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=10, max=1000)
     */
    private $width = 80;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=20)
     */
    private $playerNum = 1;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=100)
     */
    private $minGhosts = 0;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=1000)
     */
    private $ghostRate = 250;

    /**
     * @var PlayerEntity[]
     * @Assert\Valid()
     */
    private $players = array();

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getPlayerNum()
    {
        return $this->playerNum;
    }

    /**
     * @param int $playerNum
     */
    public function setPlayerNum($playerNum)
    {
        $this->playerNum = $playerNum;
    }

    /**
     * @return int
     */
    public function getMinGhosts()
    {
        return $this->minGhosts;
    }

    /**
     * @param int $minGhosts
     */
    public function setMinGhosts($minGhosts)
    {
        $this->minGhosts = $minGhosts;
    }

    /**
     * @return int
     */
    public function getGhostRate()
    {
        return $this->ghostRate;
    }

    /**
     * @param int $ghostRate
     */
    public function setGhostRate($ghostRate)
    {
        $this->ghostRate = $ghostRate;
    }

    /**
     * @param PlayerEntity[] $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * @return PlayerEntity[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param PlayerEntity $player
     */
    public function addPlayer(PlayerEntity $player)
    {
        $this->players[] = $player;
    }

    /**
     * @param int $pos
     * @return PlayerEntity
     * @throws \Exception
     */
    public function getPlayerAt($pos)
    {
        if (!array_key_exists($pos, $this->players)) {
            throw new \Exception('The key ' . $pos . ' in invalid for players array.');
        }
        return $this->players[$pos];
    }
}
