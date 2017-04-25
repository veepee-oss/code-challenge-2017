<?php

namespace AppBundle\Command;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Entity\Maze as DomainMaze;
use AppBundle\Domain\Entity\Player as DomainPlayer;
use AppBundle\Domain\Service\GameEngine\GameEngine;
use AppBundle\Entity\Game;
use AppBundle\Repository\GameRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GameEngineCommand
 *
 * @package AppBundle\Command
 */
class GameEngineCommand extends ContainerAwareCommand
{
    // Sleep time: 250 ms = 1/4 sec
    const SLEEP_TIME = 250000;

    // Maz iddle time: 15 min * 60 sec * 4 (1/4 sec)
    const MAX_IDDLE = 3600;

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
        /** @var ContainerInterface $container */
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();

        /** @var GameRepository $repo */
        $repo = $em->getRepository('AppBundle:Game');

        /** @var GameEngine $engine */
        $engine = $container->get('app.game.engine');

        $memoryLimit = ini_get('memory_limit');
        if (preg_match('/^(\d+)\s*(.)/', $memoryLimit, $matches)) {
            if ($matches[2] == 'M') {
                $memoryLimit = $matches[1] * 1024 * 1024;
            } elseif ($matches[2] == 'K') {
                $memoryLimit = $matches[1] * 1024;
            }
        }

        $iddle = 0;
        while (1) {
            /** @var Game[] $entities */
            $entities = $repo->findBy(array(
                'status' => DomainGame\Game::STATUS_RUNNING
            ));

            if (empty($entities)) {
                usleep(static::SLEEP_TIME);
                if (++$iddle > static::MAX_IDDLE) {
                    return 2;
                }
            } else {
                $iddle = 0;
                $startTime = microtime(true);
                foreach ($entities as $entity) {
                    try {
                        /** @var DomainGame\Game $game */
                        $game = $entity->toDomainEntity();
                        $engine->move($game);

                        $entity->fromDomainEntity($game);
                        $em->persist($entity);
                        $em->flush();
                    } catch (\Exception $exc) {
                        // TODO log the exception
                        $output->writeln('<error>' . $exc->getMessage() . '</error>');
                        if ($output->getVerbosity() > OutputInterface::VERBOSITY_VERY_VERBOSE) {
                            $output->writeln($exc->getFile() . ': ' . $exc->getLine());
                            $output->writeln($exc->getTraceAsString());
                        }
                    }

                    $em->detach($entity);
                    $entity = null;
                    unset($entity);
                }

                $em->clear();

                $memoryUsage = memory_get_usage(true);
                $percent = ((float) $memoryUsage) / ((float) $memoryLimit);
                if ($percent > 0.95) {
                    $output->writeln('<info>Memory usage excedes 80%</info>');
                    return 1;
                }

                $endTime = microtime(true);
                $diffTime = $endTime - $startTime;
                if ($diffTime < 1) {
                    $microSec = (int) ((1 - $diffTime) * 1000000);
                    usleep($microSec);
                }
            }
        }

        return 0;
    }
}
