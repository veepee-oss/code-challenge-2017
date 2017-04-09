<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Position\Position;

/**
 * Domain entity; ApiPlayer
 *
 * @package AppBundle\Domain\Entity\Player
 */
class ApiPlayer extends Player
{
    /** @var string */
    protected $url;

    /**
     * ApiPlayer constructor.
     *
     * @param string $url
     * @param Position $position
     */
    public function __construct($url, Position $position)
    {
        parent::__construct(parent::TYPE_API, $position);
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
     * Serialize the object into an array
     *
     * @return array
     */
    public function serialize()
    {
        return array(
            'type' => $this->type(),
            'position' => $this->position()->serialize(),
            'url' => $this->url()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return Position
     */
    public static function unserialize(array $data)
    {
        return new static(
            $data['url'],
            Position::unserialize($data['position'])
        );
    }
}
