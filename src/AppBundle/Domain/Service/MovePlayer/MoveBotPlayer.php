<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Player\BotPlayer;
use AppBundle\Domain\Entity\Player\Player;

/**
 * Class MoveBotPlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
class MoveBotPlayer extends MovePlayer
{
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
        if (!$player instanceof BotPlayer) {
            throw new MovePlayerException('The $player object must be an instance of ' . BotPlayer::class);
        }

        $command = $player->command();
        $data = $this->createRequestData($player, $maze);

        $handler = tmpfile();
        fwrite($handler, $data);
        $metaDatas = stream_get_meta_data($handle);
        $filename = $metaDatas['uri'];

        $result = shell_exec($command . ' < ' . $filename);
        fclose($handler);

        if (null === $result) {
            throw new MovePlayerException('An error occurred calling the player BOT.');
        }

        $obj = json_decode($result);
        $direction = $obj->move;

        return $direction;
    }
}
