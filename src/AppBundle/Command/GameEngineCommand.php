<?php

namespace AppBundle\Command;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Maze as DomainMaze;
use AppBundle\Domain\Entity\Player as DomainPlayer;
use AppBundle\Domain\Entity\Position\Position;
use AppBundle\Entity\Game;
use Davamigo\HttpClient\Domain\HttpClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GameEngineCommand
 *
 * @package AppBundle\Command
 */
class GameEngineCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('app:code-challenge:engine')
            ->setDescription('Privlia Code Cahllenge 2017 engine daemon.');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return null|int null or 0 if everything went fine, or an error code
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Game');

        /** @var HttpClient $httpClient */
        $httpClient = $container->get('davamigo.http.client');

        // Infinite loop
        while (1) {
            /** @var Game[] $entities */
            $entities = $repo->findBy(array(
                'status' => DomainGame\Game::STATUS_RUNNING
            ));

            foreach ($entities as $entity) {
                /** @var DomainGame\Game $game */
                $game = $entity->toDomainEntity();

                echo $entity->getUuid() . PHP_EOL;

                /** @var DomainPlayer\Player[] $players */
                $players = $game->players();
                foreach ($players as $player) {
                    if ($player->type() == DomainPlayer\Player::TYPE_API) {
                        /** @var DomainPlayer\ApiPlayer $player*/
                        $url = $player->url();
                        $response = $httpClient->get($url)->send();
                        $data = json_decode($response->getBody(true));
                        echo '> ' . $data->move . PHP_EOL;

                        $position = $player->move($data->move);
                        if ($this->validatePosition($game->maze(), $position)) {
                            $entity->setPlayers($players);
                            $em->persist($entity);
                            $em->flush();
                        }
                    }
                }
            }

            usleep(1000000);
        }

        return 0;
    }

    protected function validatePosition(DomainMaze\Maze $maze, Position $position)
    {
        $y = $position->y();
        $x = $position->x();

        if ($y < 0 || $y >= $maze->height()) {
            return false;
        }

        if ($x < 0 || $x >= $maze->width()) {
            return false;
        }

        if ($maze[$y][$x]->getContent() == DomainMaze\MazeCell::CELL_WALL) {
            return false;
        }

        return true;
    }
}
