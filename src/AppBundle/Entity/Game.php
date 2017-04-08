<?php

namespace AppBundle\Entity;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Maze as DomainMaze;
use AppBundle\Domain\Entity\Player as DomainPlayer;

use Doctrine\ORM\Mapping as ORM;
use J20\Uuid\Uuid;

/**
 * Entity Game
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GameRepository")
 */
class Game
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, unique=true)
     */
    private $uuid;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var array
     *
     * @ORM\Column(name="maze", type="json_array")
     */
    private $maze;

    /**
     * @var array
     *
     * @ORM\Column(name="players", type="json_array")
     */
    private $players;

    /**
     * Game constructor.
     *
     * @param $source
     */
    public function __construct($source = null)
    {
        if (null === $source) {
            $this->id = null;
            $this->uuid = Uuid::v4();
            $this->status = null;
            $this->width = null;
            $this->height = null;
            $this->maze = array();
            $this->players = array();
        } elseif ($source instanceof Game) {
            $this->id = $source->getId();
            $this->uuid = $source->getUuid();
            $this->status = $source->getStatus();
            $this->width = $source->getWidth();
            $this->height = $source->getHeight();
            $this->maze = $source->getMaze();
            $this->players = $source->getPlayers();
        } elseif ($source instanceof DomainGame\Game) {
            $maze = $source->maze();
            $players = $source->players();

            $this->id = null;
            $this->uuid = $source->uuid();
            $this->status = $source->status();
            $this->width = $maze->width();
            $this->height = $maze->height();
            $this->maze = array();
            $this->players = array();

            for ($i = 0; $i < $this->height; $i++) {
                $this->maze[$i] = array();
                for ($j = 0; $j < $this->width; $j++) {
                    $this->maze[$i][$j] = $maze[$i][$j]->getContent();
                }
            }

            for ($i = 0; $i < count($players); $i++) {
                $this->players[] = array(
                    'type' => $players[$i]->type(),
                    'data' => $players[$i]->execData()
                );
            }
        }
    }

    /**
     * Convert entity
     *
     * @return DomainGame\Game
     */
    public function toDomainEntity()
    {
        $maze = new DomainMaze\Maze($this->width, $this->height, $this->maze);

        $players = array();
        foreach ($this->players as $player) {
            switch ($player['type']) {
                case DomainPlayer\Player::TYPE_API:
                    $players[] = new DomainPlayer\ApiPlayer($player['data']);
                    break;

                case DomainPlayer\Player::TYPE_BOT:
                    $players[] = new DomainPlayer\BotPlayer($player['data']);
                    break;
            }
        }

        return new DomainGame\Game($maze, $players, $this->status, $this->uuid);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set game uuid
     *
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Get game uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set game status
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get game status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set maze width
     *
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * get maze width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set maze height
     *
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Get maze height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set maze cell contents
     *
     * @param array $maze
     */
    public function setMaze(array $maze)
    {
        $this->maze = $maze;
    }

    /**
     * Get maze cell contents
     *
     * @return array
     */
    public function getMaze()
    {
        return $this->maze;
    }

    /**
     * Get players
     *
     * @param array $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * Set players
     *
     * @return array
     */
    public function getPlayers()
    {
        return $this->players;
    }
}
