<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\LoggerService\LoggerServiceInterface;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use AppBundle\Domain\Service\MovePlayer\AskPlayerNameInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use AppBundle\Domain\Service\MovePlayer\PlayerRequestInterface;
use Davamigo\HttpClient\Domain\HttpClient;
use Davamigo\HttpClient\Domain\HttpException;

/**
 * Class ApiPlayer
 *
 * @package AppBundle\Service\MovePlayer
 */
class ApiPlayer implements AskNextMovementInterface, AskPlayerNameInterface
{
    /** @var HttpClient */
    protected $httpClient;

    /** @var PlayerRequestInterface */
    protected $playerRequest;

    /** @var LoggerServiceInterface */
    protected $logger;

    /**
     * ApiPlayer constructor.
     *
     * @param HttpClient $httpClient
     * @param PlayerRequestInterface $playerRequest
     * @param LoggerServiceInterface $logger
     */
    public function __construct(
        HttpClient $httpClient,
        PlayerRequestInterface $playerRequest,
        LoggerServiceInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->playerRequest = $playerRequest;
        $this->logger = $logger;
    }

    /**
     * Asks for the name of the player
     *
     * @param Player $player
     * @param Game $game
     * @return string The player name
     * @throws MovePlayerException
     */
    public function askPlayerName(Player $player, Game $game = null)
    {
        $responseData = $this->callToApi($player, $game, 'name', null);
        if (!isset($responseData['name'])) {
            $message = 'Invalid API response! Player: ' . $player->name();
            throw new MovePlayerException($message);
        }

        $name = $responseData['name'];
        return $name;
    }

    /**
     * Reads the next movement of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Game $game
     * @return string The next movement
     * @throws MovePlayerException
     */
    public function askNextMovement(Player $player, Game $game = null)
    {
        $request = $this->playerRequest->create($player, $game);

        $responseData = $this->callToApi($player, $game, 'move', $request);
        if (!isset($responseData['move'])) {
            $message = 'Invalid API response! Player: ' . $player->name();
            throw new MovePlayerException($message);
        }

        $direction = $responseData['move'];
        return $direction;
    }

    /**
     * Calls to the API
     *
     * @param Player $player
     * @param Game $game
     * @param string $function
     * @param string $requestBody
     * @return array The read data
     * @throws MovePlayerException
     */
    private function callToApi(Player $player, Game $game = null, $function = null, $requestBody = null)
    {
        if (!$player instanceof \AppBundle\Domain\Entity\Player\ApiPlayer) {
            throw new MovePlayerException(
                'The $player object must be an instance of \AppBundle\Domain\Entity\Player\ApiPlayer'
            );
        }

        $requestUrl = $player->url();
        if ($function) {
            $requestUrl .= '/' . $function;;
        }

        $requestHeaders = array(
            'Content-Type' => 'application/json; charset=UTF-8'
        );

        try {
            $response = $this->httpClient->post($requestUrl, $requestHeaders, $requestBody)->send();
        } catch (HttpException $exc) {
            $this->logger->log(
                $game ? $game->uuid() : 'temp',
                $player->uuid(),
                array(
                    'requestUrl' => $requestUrl,
                    'requestHeaders' => $requestHeaders,
                    'requestBody' => $requestBody,
                    'errorMessage' => $exc->getMessage()
                )
            );
            throw new MovePlayerException('An error occurred calling the player API.', 0, $exc);
        }

        $responseBody = $response->getBody(true);


        $responseData = json_decode($responseBody, true);
        if (null === $responseData || !is_array($responseData)) {
            $message = 'Invalid API response! Player: ' . $player->name();
            $this->logger->log(
                $game ? $game->uuid() : 'temp',
                $player->uuid(),
                array(
                    'requestUrl' => $requestUrl,
                    'requestHeaders' => $requestHeaders,
                    'requestBody' => $requestBody,
                    'responseCode' => $response->getStatusCode(),
                    'responseHeaders' => $response->getHeaderLines(),
                    'responseBody' => $responseBody,
                    'errorMessage' => $message
                )
            );
            throw new MovePlayerException($message);
        }

        $this->logger->log(
            $game ? $game->uuid() : 'temp',
            $player->uuid(),
            array(
                'requestUrl' => $requestUrl,
                'requestHeaders' => $requestHeaders,
                'requestBody' => $requestBody,
                'responseCode' => $response->getStatusCode(),
                'responseHeaders' => $response->getHeaderLines(),
                'responseBody' => $responseBody
            )
        );

        return $responseData;
    }
}
