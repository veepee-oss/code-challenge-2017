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
        $memoryLimit = ini_get('memory_limit');
        if (preg_match('/^(\d+)\s*(.)/', $memoryLimit, $matches)) {
            if ($matches[2] == 'M') {
                $memoryLimit = $matches[1] * 1024 * 1024;
            } elseif ($matches[2] == 'K') {
                $memoryLimit = $matches[1] * 1024;
            }
        }

        while (1) {
            /** @var Game[] $entities */
            $entities = $repo->findBy(array(
                'status' => DomainGame\Game::STATUS_RUNNING
            ));

            if (empty($entities)) {
                usleep(250000);
            } else {
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
                    $daemon = $container->get('app.game.engine.daemon');
                    $daemon->start(true);
                    return 1;
                }
            }
        }

        return 0;
    }
}
