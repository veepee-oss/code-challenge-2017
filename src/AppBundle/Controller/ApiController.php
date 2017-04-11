<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Player\Player;
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
    const NAME = 'Dominator API';

    /**
     * Return the name of the API
     *
     * @Route("/", name="api_home")
     * @return JsonResponse
     */
    public function indexAction()
    {
        return new JsonResponse(static::NAME);
    }

    /**
     * Move the player
     *
     * @Route("/move", name="api_move")
     * @param Request $request
     * @return JsonResponse
     */
    public function moveAction(Request $request)
    {
        // Get the data form the request
        $body = $request->getContent();
        $data = json_decode($body);

        // Extract some vars
        $walls = $data->maze->walls;
        $height = $data->maze->size->height;
        $width = $data->maze->size->width;
        $pos = $data->player->position;
        $prev = $data->player->previous;

        // Create visible maz matrix
        $maze = array();
        foreach ($walls as $wall) {
            $maze[$wall->y][$wall->x] = 1;
        }

        // Available moves
        $moves = array(
            Player::DIRECTION_UP,
            Player::DIRECTION_RIGHT,
            Player::DIRECTION_DOWN,
            Player::DIRECTION_LEFT
        );

        // Compute current direction
        $dir = null;
        if ($pos->y < $prev->y) {
            $dir = Player::DIRECTION_UP;
        } elseif ($pos->y > $prev->y) {
            $dir = Player::DIRECTION_DOWN;
        } elseif ($pos->x < $prev->x) {
            $dir = Player::DIRECTION_LEFT;
        } elseif ($pos->x > $prev->x) {
            $dir = Player::DIRECTION_RIGHT;
        }

        if ($dir) {
            // 20% probability of turning right or left
            $turn = (rand(0, 9) < 2);
            if ($turn) {
                $add = (rand(0, 1) == 0) ? 1 : 3;
                $dir = $moves[($add + array_search($dir, $moves)) % 4];
            }
        }

//        echo PHP_EOL;
//        foreach ($maze as $y => $row) {
//            for ($x = 0; $x < $width; $x++) {
//                echo isset($row[$x]) ? 'X' : (($y == $pos->y && $x == $pos->x) ? 'P' : ' ');
//            }
//            echo PHP_EOL;
//        }

        if (!$this->testMove($maze, $height, $width, $pos, $dir)) {
            unset($moves[array_search($dir, $moves)]);

            shuffle($moves);
            foreach ($moves as $move) {
                if ($dir != $move && $this->testMove($maze, $height, $width, $pos, $move)) {
                    $dir = $move;
                    break;
                }
            }
        }

        $result = array(
            'name' => static::NAME,
            'move' => $dir
        );

        return new JsonResponse($result);
    }

    /**
     * @param array $maze
     * @param int $height
     * @param int $width
     * @param \stdClass $pos
     * @param string $dir
     * @return bool
     */
    private function testMove($maze, $height, $width, $pos, $dir)
    {
        $new = clone $pos;
        switch ($dir) {
            case Player::DIRECTION_UP:
                if (--$new->y < 0) {
                    return false;
                }
                break;

            case Player::DIRECTION_DOWN:
                if (++$new->y >= $height) {
                    return false;
                }
                break;

            case Player::DIRECTION_LEFT:
                if (--$new->x < 0) {
                    return false;
                }
                break;

            case Player::DIRECTION_RIGHT:
                if (++$new->x >= $width) {
                    return false;
                }
                break;

            default:
                return false;
        }

        if (isset($maze[$new->y][$new->x]) && $maze[$new->y][$new->x] == 1) {
            return false;
        }

        return true;
    }
}
