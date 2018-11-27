<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\League;

class LeagueService extends DoctrineService
{
    public function delete(int $id): void
    {
        $this->entityManager->transactional(function (EntityManagerInterface $entityManager) use ($id): void {
            $repository = $entityManager->getRepository(League::class);
            $league = $repository->find($league);

            // Note: Hidden error?
            if (null === $league) {
                return;
            }

            $entityManager->remove($league);
            $entityManager->flush();
        });
    }
}
