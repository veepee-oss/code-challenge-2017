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
    private $players = 1;

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
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param int $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
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
}
