<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Position\Direction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 *
 * @package AppBundle\Controller
 * @Route("/api")
 */
class ApiController extends Controller
{
    const NAME = 'Test API';

    /**
     * Return the name of the API
     *
     * @Route("/name", name="api_start")
     * @return JsonResponse
     */
    public function startAction()
    {
        $candidates = array(
            'Tyrion Lanister'       => 'the.imp@ifraktal.com',
            'Jaime Lanister'        => 'kingslayer@ifraktal.com',
            'Cersei Lanister'       => 'the.queen@ifraktal.com',
            'Ned Stark'             => 'eddard.stark@ifraktal.com',
            'Robb Stark'            => 'the-king-in-the-north@ifraktal.com',
            'Sansa Stark'           => 'sansa.stark@ifraktal.com',
            'Arya Stark'            => 'no-one@ifraktal.com',
            'Brandon Stark'         => 'bran.stark@ifraktal.com',
            'Rickon Stark'          => 'rickon.stark@ifraktal.com',
            'Jon Snow'              => 'jon.snow@ifraktal.com',
            'Daenerys Targarian'    => 'daenerys.targarian@ifraktal.com',
            'Robert Baratheon'      => 'robert.the.king@ifraktal.com',
            'Stanis Baratheon'      => 'stanis.baratheon@ifraktal.com',
            'Joffrey Baratheon'     => 'joffreey.baratheon@ifraktal.com',
            'Myrcella Baratheon'    => 'myrcella.baratheon@ifraktal.com',
            'Tommem Baratheon'      => 'tommem.baratheon@ifraktal.com',
            'Margaery Tyrell'       => 'i-want-to-be-the-queen@ifraktal.com',
            'Loras Tyrell'          => 'the-night-of-the-flowers@ifraktal.com',
            'Brienne of Tarth'      => 'brienne@ifraktal.com',
            'Petyr Baelish'         => 'little-finger@ifraktal.com',
            'Varys'                 => 'little-birds@ifraktal.com',
            'Theon Grayjoy'         => 'thon.greyjoy@ifraktal.com',
            'Ramsay Bolton'         => 'ramsay.snow@ifraktal.com',
            'Sandor Clegane'        => 'the.hound@ifraktal.com',
            'Gregor Clegane'        => 'the.mountain@ifraktal.com',
            'Khal Drogo'            => 'khal.drogo@ifraktal.com',
            'Hodor'                 => 'hodor@ifraktal.com',
            'Bronn'                 => 'mercenary@ifraktal.com',
            'Jorah Mormond'         => 'jorah.mormond@ifraktal.com',
            'Grey Worm'             => 'grey.work@ifraktal.com',
            'Lady Melisandre'       => 'melisandre@ifraktal.com',
            'Davos Seaworth'        => 'davos.seaworth@ifraktal.com',
            'Ygritte'               => 'ygritte@ifraktal.com',
            'Mance Raider'          => 'the-king-beyond-the-wall@ifraktal.com'
        );

        $names = array_keys($candidates);
        $index = rand(0, count($candidates) - 1);
        $name = $names[$index];
        $email = $candidates[$name];

        return new JsonResponse(array(
            'name'  => $name,
            'email' => $email
        ));
    }

    /**
     * Move the player
     *
     * @Route("/move", name="api_move")
     * @param Request $request
     * @return JsonResponse
     * @throws \HttpException
     */
    public function moveAction(Request $request)
    {
        // Get the data form the request
        $body = $request->getContent();
        $data = json_decode($body);
        if (false === $data) {
            throw new \HttpException('Invalid request data!', 400);
        }

        // Extract some vars
        $uuid = $data->player->id;
        $walls = $data->maze->walls;
        $height = $data->maze->size->height;
        $width = $data->maze->size->width;
        $pos = $data->player->position;
        $prev = $data->player->previous;
        $goal = $data->maze->goal;

        // Compute current direction
        $dir = null;
        if ($pos->y < $prev->y) {
            $dir = Direction::UP;
        } elseif ($pos->y > $prev->y) {
            $dir = Direction::DOWN;
        } elseif ($pos->x < $prev->x) {
            $dir = Direction::LEFT;
        } elseif ($pos->x > $prev->x) {
            $dir = Direction::RIGHT;
        } else {
            $dir = Direction::UP;
        }

        $iter = 1;
        $maze = array();

        // Get data from session
        $startMaze = true;
        $savedData = $this->readFile($uuid);
        if ($savedData) {
            $startMaze = false;
            $savedData = json_decode($savedData, false);

            if (!isset($savedData->iter)
                || !isset($savedData->maze)
                || !isset($savedData->xPos)
                || !isset($savedData->yPos)) {
                $startMaze = true;
            } else {
                $iter = $savedData->iter;
                $maze = $savedData->maze;
                $xPos = $savedData->xPos;
                $yPos = $savedData->yPos;

                if ($xPos != $pos->x || $yPos != $pos->y) {
                    $startMaze = true;
                }
            }
        }

        if ($startMaze) {
            $iter = 1;
            $maze = array();
            for ($y = 0; $y < $height; ++$y) {
                $maze[$y] = array();
                for ($x = 0; $x < $width; ++$x) {
                    $maze[$y][$x] = 0;
                }
            }
        }

        // Add visible walls to the maze
        foreach ($walls as $wall) {
            $maze[$wall->y][$wall->x] = -1;
        }

        // Saving current iteration
        if ($maze[$pos->y][$pos->x] == 0) {
            $maze[$pos->y][$pos->x] = $iter;
        }

        // Compute the next direction
        $dir = $this->findNextMove($maze, $pos, $dir, $goal);
        $pos = $this->nextPosition($pos, $dir);

//        echo PHP_EOL;
//        foreach ($maze as $y => $row) {
//            foreach ($row as $x => $cell) {
//                echo ($cell < 0) ? 'X' : (($y == $pos->y && $x == $pos->x) ? 'P' : ($cell > 0 ? '.' : ' '));
//            }
//            echo PHP_EOL;
//        }

        $this->writeFile($uuid, json_encode(array(
            'iter' => 1 + $iter,
            'maze' => $maze,
            'xPos' => $pos->x,
            'yPos' => $pos->y
        )));

        return new JsonResponse(array(
            'move' => $dir
        ));
    }

    /**
     * Computes the next movement
     *
     * @param array $maze
     * @param \stdClass $pos
     * @param string $dir
     * @param \stdClass $goal
     * @return string
     */
    private function findNextMove($maze, $pos, $dir, $goal)
    {
        // Array of movements
        $moves = Direction::directions();

        $rightDir = $moves[(array_search($dir, $moves) + 1) % 4];
        $leftDir = $moves[(array_search($dir, $moves) + 3) % 4];
        $backDir = $moves[(array_search($dir, $moves) + 2) % 4];

        $forwardPos = $this->nextPosition($pos, $dir);
        $rightPos = $this->nextPosition($pos, $rightDir);
        $leftPos = $this->nextPosition($pos, $leftDir);
        $backPos = $this->nextPosition($pos, $backDir);

        // If the goal is at a side, move to it
        if ($forwardPos->y == $goal->y && $forwardPos->x == $goal->x) {
            return $dir;
        }

        if ($rightPos->y == $goal->y && $rightPos->x == $goal->x) {
            return $rightDir;
        }

        if ($leftPos->y == $goal->y && $leftPos->x == $goal->x) {
            return $leftDir;
        }

        if ($backPos->y == $goal->y && $backPos->x == $goal->x) {
            return $backDir;
        }

        // Go forward if possible
        $forwardContent= $maze[$forwardPos->y][$forwardPos->x];
        if ($forwardContent == 0) {
            return $dir;
        }

        // Turn right or left if possible (random)
        $rightContent= $maze[$rightPos->y][$rightPos->x];
        $leftContent= $maze[$leftPos->y][$leftPos->x];

        if (0 == rand(0, 1)) {
            if ($rightContent == 0) {
                return $rightDir;
            }
            if ($leftContent == 0) {
                return $leftDir;
            }
        } else {
            if ($leftContent == 0) {
                return $leftDir;
            }
            if ($rightContent == 0) {
                return $rightDir;
            }
        }

        // Else: go back
        $backContent= $maze[$backPos->y][$backPos->x];
        $currentContent= $maze[$pos->y][$pos->x];

        $moves = array();
        if ($forwardContent > 0 && $forwardContent < $currentContent) {
            $moves[$forwardContent] = $dir;
        }

        if ($rightContent > 0 && $rightContent < $currentContent) {
            $moves[$rightContent] = $rightDir;
        }

        if ($leftContent > 0 && $leftContent < $currentContent) {
            $moves[$leftContent] = $leftDir;
        }

        if ($backContent > 0 && $backContent < $currentContent) {
            $moves[$backContent] = $backDir;
        }

        ksort($moves, SORT_NUMERIC);
        $moves = array_reverse($moves);
        return reset($moves);
    }

    /**
     * Computes the next position
     *
     * @param \stdClass $pos
     * @param string $dir
     * @return \stdClass
     */
    private function nextPosition($pos, $dir)
    {
        $new = clone $pos;
        switch ($dir) {
            case Direction::UP:
                --$new->y;
                break;

            case Direction::DOWN:
                ++$new->y;
                break;

            case Direction::LEFT:
                --$new->x;
                break;

            case Direction::RIGHT:
                ++$new->x;
                break;
        }
        return $new;
    }

    /**
     * Reads the temporary file with the saved data
     *
     * @param string $uuid
     * @return string|false
     */
    private function readFile($uuid)
    {
        $filename = sys_get_temp_dir() . '/' . $uuid . '.json';
        $handler = @fopen($filename, 'rb');
        if (!$handler) {
            return false;
        }

        $data = @fgets($handler);
        if (!$data) {
            return false;
        }

        return $data;
    }

    /**
     * Writes the process data to a temporary file
     *
     * @param string $uuid
     * @param string $data
     * @return bool
     */
    private function writeFile($uuid, $data)
    {
        $filename = sys_get_temp_dir() . '/' . $uuid . '.json';
        $handler = @fopen($filename, 'wb');
        if (!$handler) {
            return false;
        }

        fwrite($handler, $data);
        fclose($handler);
        return true;
    }
}
