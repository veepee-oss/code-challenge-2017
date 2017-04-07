<?php

namespace AppBundle\Domain\Entity;

/**
 * Domain entity; ApiPlayer
 *
 * @package AppBundle\Domain\Entity
 */
class ApiPlayer implements Player
{
    /** @var string */
    protected $url;

    /**
     * ApiPlayer constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function url()
    {
        return $this->url;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function type()
    {
        return static::TYPE_API;
    }

    /**
     * Get execution data (url, command, ...)
     *
     * @return string
     */
    public function execData()
    {
        return $this->url();
    }
}
