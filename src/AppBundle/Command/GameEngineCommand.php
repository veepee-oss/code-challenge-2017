<?php

namespace AppBundle\Command;

use AppBundle\Domain\Entity\Game as DomainGame;
use AppBundle\Domain\Service\GameEngine\GameEngine;
use AppBundle\Entity\Game;
use AppBundle\Repository\GameRepository;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
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

    // Max idle time: 15 min * 60 sec * 4 (1/4 sec)
    const MAX_IDLE = 3600;

    // Memory usage limit: 90%
    const MEMORY_LIMIT = 0.90;

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

        /** @var LoggerInterface $logger */
        $logger = $container->get('logger');

        // Get memory limit
        $memoryLimit = ini_get('memory_limit');
        if (preg_match('/^(\d+)\s*(.)/', $memoryLimit, $matches)) {
            if ($matches[2] == 'M') {
                $memoryLimit = $matches[1] * 1024 * 1024;
            } elseif ($matches[2] == 'K') {
                $memoryLimit = $matches[1] * 1024;
            }
        }

        // Infinite loop (daemon)
        $idle = 0;
        while (1) {
            /** @var Game[] $games */
            $games = $repo->findBy(array(
                'status' => DomainGame\Game::STATUS_RUNNING
            ));

            if (empty($games)) {
                // Sleep 1/4 second
                usleep(static::SLEEP_TIME);

                // Kill the process if it's idle many time
                if (++$idle > static::MAX_IDLE) {
                    $errorMessage = 'Max idle time reached. Process kills itself!';
                    $logger->emergency($errorMessage);
                    $output->writeln('<info>' . $errorMessage . '</info>');
                    return 1;
                }
            } else {
                $idle = 0;
                $startTime = microtime(true);
                foreach ($games as $gameEntity) {
                    try {
                        /** @var DomainGame\Game $game */
                        $game = $gameEntity->toDomainEntity();
                        $engine->move($game);
                    } catch (\Exception $exc) {
                        $logger->error('Error occurred in the game engine.');
                        $logger->error($exc);
                        $output->writeln('<error>' . $exc->getMessage() . '</error>');
                        if ($output->getVerbosity() > OutputInterface::VERBOSITY_VERY_VERBOSE) {
                            $output->writeln($exc->getFile() . ': ' . $exc->getLine());
                            $output->writeln($exc->getTraceAsString());
                        }
                    }

                    // Persist game entity
                    $gameEntity->fromDomainEntity($game);
                    $em->persist($gameEntity);
                    $em->flush();

                    // Free memory
                    $em->detach($gameEntity);
                    $gameEntity = null;
                    unset($gameEntity);
                }

                // Clear the Doctrine cache
                $em->clear();

                // Test memory usage to avoid errors
                $memoryUsage = memory_get_usage(true);
                $percent = ((float) $memoryUsage) / ((float) $memoryLimit);
                if ($percent > static::MEMORY_LIMIT) {
                    $errorMessage = sprintf(
                        '<info>Memory usage excedes %d%%.</info>',
                        (int)(100 * static::MEMORY_LIMIT)
                    );
                    $logger->emergency($errorMessage);
                    $output->writeln($errorMessage);
                    return 2;
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
