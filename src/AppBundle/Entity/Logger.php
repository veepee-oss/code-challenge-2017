<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity Logger
 *
 * @package AppBundle\Entity
 * @ORM\Table(name="logger")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LoggerRepository")
 */
class Logger
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="game_uuid", type="string", length=36)
     */
    private $gameUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="player_uuid", type="string", length=36)
     */
    private $playerUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="request_url", type="text", nullable=true)
     */
    private $requestUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="request_headers", type="text", nullable=true)
     */
    private $requestHeaders;

    /**
     * @var string
     *
     * @ORM\Column(name="request_body", type="text", nullable=true)
     */
    private $requestBody;

    /**
     * @var int
     *
     * @ORM\Column(name="response_code", type="integer", nullable=true)
     */
    private $responseCode;

    /**
     * @var string
     *
     * @ORM\Column(name="response_headers", type="text", nullable=true)
     */
    private $responseHeaders;

    /**
     * @var string
     *
     * @ORM\Column(name="response_body", type="text", nullable=true)
     */
    private $responseBody;

    /**
     * @var string
     *
     * @ORM\Column(name="error_message", type="text", nullable=true)
     */
    private $errorMessage;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $gameUuid
     * @return $this
     */
    public function setGameUuid($gameUuid)
    {
        $this->gameUuid = $gameUuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getGameUuid()
    {
        return $this->gameUuid;
    }

    /**
     * @param string $playerUuid
     * @return $this
     */
    public function setPlayerUuid($playerUuid)
    {
        $this->playerUuid = $playerUuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlayerUuid()
    {
        return $this->playerUuid;
    }

    /**
     * @param string $requestUrl
     * @return $this
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * @param string|array $requestHeaders
     * @return $this
     */
    public function setRequestHeaders($requestHeaders)
    {
        if (!is_array($requestHeaders)) {
            $this->requestHeaders = $requestHeaders;
        } else {
            $this->requestHeaders = json_encode($requestHeaders);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * @param string|array $requestBody
     * @return $this
     */
    public function setRequestBody($requestBody)
    {
        if (!is_array($requestBody)) {
            $this->requestBody = $requestBody;
        } else {
            $this->requestBody = json_encode($requestBody);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * @param int $responseCode
     * @return $this
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param string|array $responseHeaders
     * @return $this
     */
    public function setResponseHeaders($responseHeaders)
    {
        if (!is_array($responseHeaders)) {
            $this->responseHeaders = $responseHeaders;
        } else {
            $this->responseHeaders = json_encode($responseHeaders);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * @param string|array $responseBody
     * @return $this
     */
    public function setResponseBody($responseBody)
    {
        if (!is_array($responseBody)) {
            $this->responseBody = $responseBody;
        } else {
            $this->responseBody = json_encode($responseBody);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Set raw data of the entity
     *
     * @param $gameUuid
     * @param $playerUuid
     * @param array $data
     * @return $this
     */
    public function setRawData($gameUuid, $playerUuid, array $data)
    {
        $this->setGameUuid($gameUuid);
        $this->setPlayerUuid($playerUuid);
        $this->setRequestUrl(isset($data['requestUrl']) ? $data['requestUrl'] : null);
        $this->setRequestHeaders(isset($data['requestHeaders']) ? $data['requestHeaders'] : null);
        $this->setRequestBody(isset($data['requestBody']) ? $data['requestBody'] : null);
        $this->setResponseCode(isset($data['responseCode']) ? $data['responseCode'] : null);
        $this->setResponseHeaders(isset($data['responseHeaders']) ? $data['responseHeaders'] : null);
        $this->setResponseBody(isset($data['responseBody']) ? $data['responseBody'] : null);
        $this->setErrorMessage(isset($data['errorMessage']) ? $data['errorMessage'] : null);
        return $this;
    }

    /**
     * Get raw data of the entity
     *
     * @return array
     */
    public function getRawData()
    {
        return array(
            'id'                => $this->getId(),
            'gameUuid'          => $this->getGameUuid(),
            'playerUuid'        => $this->getPlayerUuid(),
            'requestUrl'        => $this->getRequestUrl(),
            'requestHeaders'    => json_decode($this->getRequestHeaders(), true),
            'requestBody'       => json_decode($this->getRequestBody(), true),
            'responseCode'      => $this->getResponseCode(),
            'responseHeaders'   => json_decode($this->getResponseHeaders(), true),
            'responseBody'      => json_decode($this->getResponseBody(), true),
            'errorMessage'      => $this->getErrorMessage()
        );
    }
}
