<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\ApiPlayer;
use AppBundle\Domain\Entity\Player\Player;
use Davamigo\HttpClient\Domain\HttpClient;
use Davamigo\HttpClient\Domain\HttpException;

/**
 * Class MoveApiPlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MoveApiPlayer extends MovePlayer
{
    /** @var  HttpClient */
    protected $httpClient;

    /**
     * MoveApiPlayer constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Maze $maze
     * @return string The next movement
     * @throws MovePlayerException
     */
    protected function readNextMovement(Player $player, Maze $maze)
    {
        if (!$player instanceof ApiPlayer) {
            throw new MovePlayerException('The $player object must be an instance of ' . ApiPlayer::class);
        }

        $url = $player->url();
        $data = $this->createRequestData($player, $maze);
        $headers = array(
            'Content-Type' => 'application/json; charset=UTF-8'
        );

        try {
            $response = $this->httpClient->post($url, $headers, $data)->send();
        } catch (HttpException $exc) {
            throw new MovePlayerException('An error occurred calling the player API.', 0, $exc);
        }

        $body = $response->getBody(true);
        $data = json_decode($body, true);
        if (null === $data || empty($data) || !is_array($data) || !array_key_exists('move', $data)) {
            throw new MovePlayerException('Invalid API response: ' . $body);
        }

        $direction = $data['move'];
        return $direction;
    }
}
