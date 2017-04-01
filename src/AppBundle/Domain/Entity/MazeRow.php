<?php

namespace AppBundle\Domain\Entity;


/**
 * Domain Entity MazeRow
 *
 * @package AppBundle\Domain\Entity
 */
class MazeRow implements \ArrayAccess, \Countable, \Iterator
{
    /** @var int */
    protected $count;

    /** @var MazeCell[] */
    protected $cells;

    /** @var int */
    protected $position;

    /**
     * MazeRow constructor.
     *
     * @param int $count
     * @throws \InvalidArgumentException
     */
    public function __construct($count)
    {
        $this->validateOffset($count);
        $this->count = $count;
        $this->cells = [];
        $this->position = 0;

        for ($i = 0; $i < $this->count; ++$i) {
            $this->cells[$i] = new MazeCell(MazeCell::CELL_EMPTY);
        }
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
        try {
            $this->validateOffset($offset);
        } catch (\InvalidArgumentException $exc) {
            return false;
        }

        return ($offset >= 0 && $offset < $this->count);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve.
     * @return MazeCell
     * @throws \InvalidArgumentException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The offset ' . $offset . ' doen\'t exists.');
        }

        return $this->cells[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param int $offset The offset to assign the value to.
     * @param MazeCell $value The value to set.
     * @return void
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The offset ' . $offset . ' doen\'t exists.');
        }

        $this->cells[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param int $offset The offset to unset.
     * @return void
     * @throws \InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException('The offset ' . $offset . ' doen\'t exists.');
        }

        $this->cells[$offset] = new MazeCell(MazeCell::CELL_EMPTY);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return $this->count();
    }

    /**
     * Return the current element
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return MazeCell
     */
    public function current()
    {
        return $this[$this->position];
    }

    /**
     * Move forward to next element
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->offsetExists($this->position);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Whether offset is valid (integer or string containing an integer)
     *
     * @param int $offset
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateOffset($offset)
    {
        if (!is_numeric($offset) || $offset != intval($offset)) {
            throw new \InvalidArgumentException('The offset ' . $offset . ' is not an integer.');
        }
    }
}
