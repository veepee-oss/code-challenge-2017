<?php

namespace AppBundle\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\ApiPlayer;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Service\LoggerService\LoggerServiceInterface;
use AppBundle\Domain\Service\MovePlayer\AskNextMovementInterface;
use AppBundle\Domain\Service\MovePlayer\AskPlayerNameInterface;
use AppBundle\Domain\Service\MovePlayer\MovePlayerException;
use AppBundle\Domain\Service\MovePlayer\PlayerRequestInterface;
use Davamigo\HttpClient\Domain\HttpClient;
use Davamigo\HttpClient\Domain\HttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ApiPlayerService
 *
 * @package AppBundle\Service\MovePlayer
 */
class ApiPlayerService implements AskNextMovementInterface, AskPlayerNameInterface
{
    /** @var HttpClient */
    protected $httpClient;

    /** @var PlayerRequestInterface */
    protected $playerRequest;

    /** @var ValidatorInterface */
    protected $validator;

    /** @var LoggerServiceInterface */
    protected $logger;

    /**
     * ApiPlayerService constructor.
     *
     * @param HttpClient $httpClient
     * @param PlayerRequestInterface $playerRequest
     * @param LoggerServiceInterface $logger
     */
    public function __construct(
        HttpClient $httpClient,
        PlayerRequestInterface $playerRequest,
        ValidatorInterface $validator,
        LoggerServiceInterface $logger
    ) {
        $this->httpClient = $httpClient;
        $this->playerRequest = $playerRequest;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * Asks for the name of the player
     *
     * @param Player $player
     * @param Game $game
     * @return array['name', 'email'] The player name and email
     * @throws MovePlayerException
     */
    public function askPlayerName(Player $player, Game $game = null)
    {
        if (!$player instanceof ApiPlayer) {
            throw new MovePlayerException(
                'The $player object must be an instance of \AppBundle\Domain\Entity\Player\ApiPlayer'
            );
        }

        // Call to the REST API
        $responseData = $this->callToApi($player, $game, 'name', null);
        if (!$responseData['name'] || !isset($responseData['name'])) {
            $message = 'Invalid API response! ';
            $message .= PHP_EOL . 'Message: Empty response.';
            $message .= PHP_EOL . 'URL: ' . $player->url();
            throw new MovePlayerException($message);
        }

        // Extract the data from the response
        $name = isset($responseData['name']) ? $responseData['name'] : null;
        $email = isset($responseData['email']) ? $responseData['email'] : null;

        // Constraints definition
        $notBlankConstraint = new Assert\NotBlank();
        $emailConstraint = new Assert\Email();

        // Use the validator to validate the name
        $errorList = $this->validator->validate($name, $notBlankConstraint);
        if (0 !== count($errorList)) {
            $message = 'Invalid API response! ';
            $message .= PHP_EOL . 'Message: Name is required.';
            $message .= PHP_EOL . 'URL: ' . $player->url();
            throw new MovePlayerException($message);
        }

        // Use the validator to validate the email
        $errorList = $this->validator->validate($email, array($notBlankConstraint, $emailConstraint));
        if (0 !== count($errorList)) {
            $message = 'Invalid API response! ';
            $message .= PHP_EOL . 'Message: Valid email is required.';
            $message .= PHP_EOL . 'URL: ' . $player->url();
            throw new MovePlayerException($message);
        }

        return array(
            'name' => $responseData['name'],
            'email' => $responseData['email']
        );
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
        if (!$player instanceof ApiPlayer) {
            throw new MovePlayerException(
                'The $player object must be an instance of \AppBundle\Domain\Entity\Player\ApiPlayer'
            );
        }

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
     * @param ApiPlayer $player
     * @param Game $game
     * @param string $function
     * @param string $requestBody
     * @return array The read data
     * @throws MovePlayerException
     */
    private function callToApi(ApiPlayer $player, Game $game = null, $function = null, $requestBody = null)
    {
        $requestUrl = $player->url();
        if ($function) {
            $requestUrl .= '/' . $function;;
        }

        $requestHeaders = array(
            'Content-Type' => 'application/json; charset=UTF-8'
        );

        try {
            $options = array(
                CURLOPT_CONNECTTIMEOUT  => 3,
                CURLOPT_TIMEOUT         => 5
            );
            $response = $this->httpClient->post($requestUrl, $requestHeaders, $requestBody, $options)->send();
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
