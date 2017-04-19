<?php

namespace AppBundle\Domain\Entity\Maze;

use AppBundle\Domain\Entity\Position\Position;

/**
 * Domain Entity Maze
 *
 * @package AppBundle\Domain\Entity\Maze
 */
class Maze implements \ArrayAccess, \Countable, \Iterator
{
    /** @var int */
    protected $width;

    /** @var int */
    protected $height;

    /** @var Position */
    protected $start;

    /** @var  Position */
    protected $goal;

    /** @var MazeRow[] */
    protected $rows;

    /** @var int */
    protected $index;

    /**
     * Maze constructor.
     *
     * @param int $height
     * @param int $width
     * @param Position $start
     * @param Position $goal
     * @param array $cells
     */
    public function __construct(
        $height,
        $width,
        Position $start = null,
        Position $goal = null,
        array $cells = null
    ) {
        $this->validateHeight($height);
        $this->validateWidth($width);
        $this->height = $height;
        $this->width = $width;
        $this->start = $start ? clone $start : null;
        $this->goal = $goal ? clone $goal: null;
        $this->rows = array();
        $this->index = 0;

        for ($i = 0; $i < $this->height; ++$i) {
            $this->rows[$i] = new MazeRow($this->width);
            for ($j = 0; $j < $this->width; $j++) {
                $this[$i][$j]->setContent($cells[$i][$j]);
            }
        }
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
     * Get height
     *
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * Get start position
     *
     * @return Position
     */
    public function start()
    {
        return $this->start;
    }

    /**
     * Set start position
     *
     * @param Position $start
     */
    public function setStart(Position $start)
    {
        $this->start = $start;
    }

    /**
     * Get goal position
     *
     * @return Position
     */
    public function goal()
    {
        return $this->goal;
    }

    /**
     * Set goal position
     *
     * @param Position $goal
     */
    public function setGoal(Position $goal)
    {
        $this->goal = $goal;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param int $offset An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        $this->validateHeight($offset);
        return $this->valid();
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param int $offset The offset to retrieve.
     * @return MazeRow
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The height ' . $offset . ' doen\'t exists.');
        }

        return $this->rows[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param int $offset The offset to assign the value to.
     * @param MazeRow $value The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The height ' . $offset . ' doen\'t exists.');
        }

        $this->rows[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The height ' . $offset . ' doen\'t exists.');
        }
        $this->rows[$offset] = new MazeRow($this->width);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return $this->height;
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return MazeRow
     */
    public function current()
    {
        return $this[$this->index];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return int
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current index is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean Returns true on success or false on failure.
     */
    public function valid()
    {
        return ($this->index >= 0 && $this->index < $this->height);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Validates the width (integer or string containing an integer)
     *
     * @param int $width
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateWidth($width)
    {
        if (!is_numeric($width) || $width != intval($width)) {
            throw new \InvalidArgumentException('The width ' . $width . ' is not an integer.');
        }
    }

    /**
     * Validates the height (integer or string containing an integer)
     *
     * @param int $height
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateHeight($height)
    {
        if (!is_numeric($height) || $height != intval($height)) {
            throw new \InvalidArgumentException('The height ' . $height . ' is not an integer.');
        }
    }
}
