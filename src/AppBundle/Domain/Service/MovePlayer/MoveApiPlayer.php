<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\ApiPlayer;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\LoggerService\LoggerServiceInterface;
use Davamigo\HttpClient\Domain\HttpClient;
use Davamigo\HttpClient\Domain\HttpException;

/**
 * Class MoveApiPlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MoveApiPlayer extends MovePlayer
{
    /** @var HttpClient */
    protected $httpClient;

    /** @var LoggerServiceInterface */
    protected $logger;

    /**
     * MoveApiPlayer constructor.
     *
     * @param HttpClient $httpClient
     * @param LoggerServiceInterface $logger
     */
    public function __construct(HttpClient $httpClient, LoggerServiceInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Game $game
     * @return string The next movement
     * @throws MovePlayerException
     */
    protected function readNextMovement(Player $player, Game $game)
    {
        if (!$player instanceof ApiPlayer) {
            throw new MovePlayerException(
                'The $player object must be an instance of \AppBundle\Domain\Entity\Player\ApiPlayer'
            );
        }

        $requestUrl = $player->url();
        $requestBody = $this->createRequestData($player, $game);
        $requestHeaders = array(
            'Content-Type' => 'application/json; charset=UTF-8'
        );

        try {
            $response = $this->httpClient->post($requestUrl, $requestHeaders, $requestBody)->send();
        } catch (HttpException $exc) {
            $this->logger->log(
                $game->uuid(),
                $player->uuid(),
                array(
                    'requestUrl'        => $requestUrl,
                    'requestHeaders'    => $requestHeaders,
                    'requestBody'       => $requestBody,
                    'errorCode'         => $exc->getMessage()
                )
            );
            throw new MovePlayerException('An error occurred calling the player API.', 0, $exc);
        }

        $this->logger->log(
            $game->uuid(),
            $player->uuid(),
            array(
                'requestUrl'        => $requestUrl,
                'requestHeaders'    => $requestHeaders,
                'requestBody'       => $requestBody,
                'responseCode'      => $response->getStatusCode(),
                'responseHeaders'   => $response->getHeaderLines(),
                'responseBody'      => $response->getBody()
            )
        );

        $responseBody = $response->getBody(true);
        $responseData = json_decode($responseBody, true);
        if (null === $responseData || !is_array($responseData) || !isset($responseData['move'])) {
            throw new MovePlayerException('Invalid API response: ' . $responseBody);
        }

        $direction = $responseData['move'];
        return $direction;
    }
}
