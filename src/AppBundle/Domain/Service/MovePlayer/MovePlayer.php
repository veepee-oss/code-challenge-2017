<?php

namespace AppBundle\Domain\Service\MovePlayer;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Maze\Maze;
use AppBundle\Domain\Entity\Maze\MazeCell;
use AppBundle\Domain\Entity\Maze\MazeObject;
use AppBundle\Domain\Entity\Player\Player;
use AppBundle\Domain\Entity\Position\Position;

/**
 * Class MovePlayer
 *
 * @package AppBundle\Domain\Service\MovePlayer
 */
abstract class MovePlayer implements MovePlayerInterface
{
    const STEP = 5;

    /**
     * Moves the player
     *
     * @param Player $player
     * @param Game $game
     * @return bool true=successs, false=error
     * @throws MovePlayerException
     */
    public function movePlayer(Player& $player, Game $game)
    {
        echo sprintf(PHP_EOL . 'Player at [%02d, %02d] ', $player->position()->x(), $player->position()->y());
        echo sprintf('from [%02d, %02d] ', $player->previous()->x(), $player->previous()->y());
        echo sprintf('direction %s' . PHP_EOL, $player->direction());

        // Reads the next movement of the player: "up", "down", "left" or "right".
        $direction = $this->readNextMovement($player, $game);

        echo sprintf('New Direction: %s ', $direction);

        // Computes the new position
        $position = $this->computeNewPosition($player->position(), $direction);
        if (!$this->validatePosition($position, $game->maze())) {
            echo '>>>>>>>>>>>>>>> Invalid!' . PHP_EOL;
            return false;
        }

        echo sprintf('>> Move to [%02d, %02d]' . PHP_EOL, $position->x(), $position->y());

        $player->move($position);

        return true;
    }

    /**
     * Reads the next movemento of the player: "up", "down", "left" or "right".
     *
     * @param Player $player
     * @param Game $game
     * @return string The next movement
     * @throws MovePlayerException
     */
    abstract protected function readNextMovement(Player $player, Game $game);

    /**
     * Creates the request data to send to the player bot or api.
     *
     * @param Player $player
     * @param Game $game
     * @return string Request in json format
     */
    protected function createRequestData(Player $player, Game $game)
    {
        $maze = $game->maze();
        $height = $maze->height();
        $width = $maze->width();
        $pos = $player->position();
        $prev = $player->previous();

        $step = static::STEP;
        $size = 1 + ($step * 2);
        while ($size > $height || $size > $height) {
            --$step;
            $size = 1 + ($step * 2);
        }

        $y1 = $pos->y() - $step;
        $y2 = $pos->y() + $step;
        $x1 = $pos->x() - $step;
        $x2 = $pos->x() + $step;

        if ($y1 < 0) {
            $y2 -= $y1;
            $y1 = 0;
        } elseif ($y2 >= $height) {
            $y1 -= ($pos->y() - $height + 1);
            $y2 = $height - 1;
        }

        if ($x1 < 0) {
            $x2 -= $x1;
            $x1 = 0;
        } elseif ($x2 >= $width) {
            $x1 -= ($pos->x() - $width + 1);
            $x2 = $width - 1;
        }

        $walls = array();
        for ($y = $y1; $y <= $y2; ++$y) {
            for ($x = $x1; $x <= $x2; ++$x) {
                if ($maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
                    $walls[] = array(
                        'y' => $y,
                        'x' => $x
                    );
                }
            }
        }

        $data = array(
            'game'      => array(
                'id'        => $game->uuid()
            ),
            'player'    => array(
                'id'        => $player->uuid(),
                'position'  => array(
                    'y'         => $pos->y(),
                    'x'         => $pos->x()
                ),
                'previous'  => array(
                    'y'         => $prev->y(),
                    'x'         => $prev->x()
                ),
                'area'      => array(
                    'y1'        => $y1,
                    'x1'        => $x1,
                    'y2'        => $y2,
                    'x2'        => $x2
                )
            ),
            'maze'      => array(
                'size'      => array(
                    'height'    => $height,
                    'width'     => $width
                ),
                'goal'  => array(
                    'y'         => $maze->goal()->y(),
                    'x'         => $maze->goal()->x()
                ),
                'walls'     => $walls
            )
        );

        return json_encode($data);
    }

    /**
     * Computes the new position for a movement
     *
     * @param Position $position
     * @param string $direction
     * @return Position
     */
    protected function computeNewPosition(Position $position, $direction)
    {
        $y = $position->y();
        $x = $position->x();
        switch ($direction) {
            case MazeObject::DIRECTION_UP:
                $y--;
                break;

            case MazeObject::DIRECTION_DOWN:
                $y++;
                break;

            case MazeObject::DIRECTION_LEFT:
                $x--;
                break;

            case MazeObject::DIRECTION_RIGHT:
                $x++;
                break;
        }

        return new Position($y, $x);
    }

    /**
     * Validates the position in the map
     *
     * @param Position $position
     * @param Maze $maze
     * @return bool
     */
    protected function validatePosition(Position $position, Maze $maze)
    {
        $y = $position->y();
        $x = $position->x();

        if ($y < 0 || $y >= $maze->height()) {
            return false;
        }

        if ($x < 0 || $x >= $maze->width()) {
            return false;
        }

        if ($maze[$y][$x]->getContent() == MazeCell::CELL_WALL) {
            return false;
        }

        return true;
    }
}
