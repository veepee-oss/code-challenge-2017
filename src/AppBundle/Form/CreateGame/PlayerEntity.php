<?php

namespace AppBundle\Form\CreateGame;

use AppBundle\Domain\Entity\Player\Player;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PlayerEntity
 *
 * @package AppBundle\Form\CreateGame
 */
class PlayerEntity
{
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Range(min=1, max=2)
     */
    private $type = Player::TYPE_API;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}
