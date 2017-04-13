<?php

namespace AppBundle\Domain\Entity\Player;

use AppBundle\Domain\Entity\Position\Position;
use J20\Uuid\Uuid;

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
     * @param Position $previous
     * @param int $status
     * @param string $uuid
     */
    public function __construct($url, Position $position, Position $previous = null, $status = null, $uuid = null)
    {
        parent::__construct(parent::TYPE_API, $position, $previous, $status, $uuid);
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
        return parent::serialize() + array(
            'url' => $this->url()
        );
    }

    /**
     * Unserialize from an array and create the object
     *
     * @param array $data
     * @return ApiPlayer
     */
    public static function unserialize(array $data)
    {
        return new static(
            $data['url'],
            Position::unserialize($data['position']),
            Position::unserialize(isset($data['previous']) ? $data['previous'] : $data['position']),
            isset($data['status']) ? $data['status'] : static::STATUS_PLAYING,
            isset($data['uuid']) ? $data['uuid'] : Uuid::v4()
        );
    }
}
