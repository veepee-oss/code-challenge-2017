<?php

namespace AppBundle\Service\LoggerService;

use AppBundle\Domain\Service\LoggerService\LoggerServiceInterface;
use AppBundle\Entity\Logger;
use AppBundle\Repository\LoggerRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class LoggerService
 *
 * @package AppBundle\Service\LoggerService
 */
class LoggerService implements LoggerServiceInterface
{
    /** @var EntityManager */
    private $em;

    /**
     * LoggerService constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Saves the data of the request
     *
     * @param string $gameUuid
     * @param string $playerUuid
     * @param array $data
     * @return void
     */
    public function log($gameUuid, $playerUuid, array $data)
    {
        $logger = new Logger();
        $logger->setRawData($gameUuid, $playerUuid, $data);
        $this->em->persist($logger);
    }

    /**
     * Clears the log of a game
     *
     * @param string $gameUuid
     * @param string $playerUuid
     * @return void
     */
    public function clear($gameUuid, $playerUuid = null)
    {
        $qb = $this->em->createQueryBuilder()
            ->delete('AppBundle:Logger', 'logger')
            ->where('logger.gameUuid = :gameUuid')
            ->setParameter('gameUuid', $gameUuid);

        if (null !== $playerUuid) {
            $qb->andWhere('logger.playerUuid = :playerUuid')
                ->setParameter('playerUuid', $playerUuid);
        }

        $qb->getQuery()->execute();
        $this->em->flush();
    }

    /**
     * Reads the log of a game
     *
     * @param string $gameUuid
     * @param string $playerUuid
     * @return array The raw data of the log
     */
    public function read($gameUuid, $playerUuid = null)
    {
        $criteria = array(
            'gameUuid' => $gameUuid
        );

        if (null !== $playerUuid) {
            $criteria += array(
                'playerUuid' => $playerUuid
            );
        }

        $repo = $this->em->getRepository('AppBundle:Logger');
        $logs = $repo->findBy($criteria, array(
            'gameUuid'      => 'asc',
            'playerUuid'    => 'asc',
            'id'            => 'asc'
        ));

        $result = array();
        foreach ($logs as $log) {
            $result[] = $log->getRawData();
        }

        return $result;
    }
}
