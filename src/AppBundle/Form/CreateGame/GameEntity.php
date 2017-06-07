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
     * @Assert\Range(min=10, max=100)
     */
    private $height = 25;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=10, max=100)
     */
    private $width = 50;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=20)
     */
    private $playerNum = 2;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=100)
     */
    private $minGhosts = 1;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=0, max=200)
     */
    private $ghostRate = 50;

    /**
     * @var string
     * @Assert\Length(min=0, max=48)
     */
    private $name = null;

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
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
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
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
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
     * @return $this
     */
    public function setPlayerNum($playerNum)
    {
        $this->playerNum = $playerNum;
        return $this;
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
     * @return $this
     */
    public function setMinGhosts($minGhosts)
    {
        $this->minGhosts = $minGhosts;
        return $this;
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
     * @return $this
     */
    public function setGhostRate($ghostRate)
    {
        $this->ghostRate = $ghostRate;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param PlayerEntity[] $players
     * @return $this
     */
    public function setPlayers($players)
    {
        $this->players = $players;
        return $this;
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
     * @return $this
     */
    public function addPlayer(PlayerEntity $player)
    {
        $this->players[] = $player;
        return $this;
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
