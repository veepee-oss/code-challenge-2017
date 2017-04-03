<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity Maze
 *
 * @ORM\Table(name="maze")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MazeRepository")
 */
class Maze
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uniqid", type="string", length=13, unique=true)
     */
    private $uniqid;

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
     * @ORM\Column(name="cells", type="json_array")
     */
    private $cells;

    /**
     * Maze constructor.
     *
     * @param \AppBundle\Domain\Entity\Maze $source
     */
    public function __construct(\AppBundle\Domain\Entity\Maze $source = null)
    {
        $this->uniqid = uniqid();
        if ($source !== null ) {
            $this->width = $source->width();
            $this->height = $source->height();
            $this->cells = array();
            for ($i = 0; $i < $this->height; $i++) {
                $this->cells[$i] = array();
                for ($j = 0; $j < $this->width; $j++) {
                    $this->cells[$i][$j] = $source[$i][$j]->getContent();
                }
            }
        } else {
            $this->width = null;
            $this->height = null;
            $this->cells = array();
        }
    }

    /**
     * Convert entity
     *
     * @return \AppBundle\Domain\Entity\Maze
     */
    public function toDomainEntity()
    {
        return new \AppBundle\Domain\Entity\Maze(
            $this->width,
            $this->height,
            $this->uniqid,
            $this->cells
        );
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
     * @param string $uniqid
     */
    public function setUniqid($uniqid)
    {
        $this->uniqid = $uniqid;
    }

    /**
     * @return string
     */
    public function getUniqid()
    {
        return $this->uniqid;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Maze
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function width()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Maze
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * Set cells
     *
     * @param array $cells
     *
     * @return Maze
     */
    public function setCells($cells)
    {
        $this->cells = $cells;

        return $this;
    }

    /**
     * Get cells
     *
     * @return array
     */
    public function getCells()
    {
        return $this->cells;
    }
}

