<?php

namespace AppBundle\Domain\Service\MoveGhost;

use AppBundle\Domain\Entity\Ghost\Ghost;

/**
 * Class MoveGhostFactory
 *
 * @package AppBundle\Domain\Service\MoveGhost
 */
class MoveGhostFactory
{
    /** @var MoveGhostInterface[] */
    protected $services;

    /**
     * MoveGhostFactory constructor.
     *
     * @param MoveGhostInterface[] $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    /**
     * Locates the right service to move a ghost
     *
     * @param Ghost $ghost
     * @return MoveGhostInterface
     * @throws MoveGhostException
     */
    public function locate(Ghost $ghost)
    {
        if (!array_key_exists($ghost->type(), $this->services)) {
            throw new MoveGhostException('Move ghost service not found for class ' . get_class($ghost));
        }

        return $this->services[$ghost->type()];
    }
}
