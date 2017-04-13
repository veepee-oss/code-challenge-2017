<?php

namespace AppBundle\Command;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Maze as DomainMaze;
use AppBundle\Domain\Entity\Player as DomainPlayer;
use AppBundle\Entity\Game;
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
            ->setDescription('Privalia Code Challenge 2017 engine daemon.');
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
        $engine = $container->get('app.game.engine');

        // Infinite loop
        while (1) {
            /** @var Game[] $entities */
            $entities = $repo->findBy(array(
                'status' => DomainGame\Game::STATUS_RUNNING
            ));

            foreach ($entities as $entity) {
                /** @var DomainGame\Game $game */
                $game = $entity->toDomainEntity();
                if ($engine->movePlayers($game)) {
                    $entity->fromDomainEntity($game);
                    $em->persist($entity);
                    $em->flush();
                }
            }

            usleep(100000);
        }

        return 0;
    }
}
