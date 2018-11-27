<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\League;

class LeagueService extends DoctrineService
{
    public function delete(int $id): void
    {
        $this->entityManager->transactional(function (EntityManagerInterface $entityManager) use ($id): void {
            $repository = $entityManager->getRepository(League::class);
            $league = $repository->find($id);

            if (null === $league) {
                throw new HttpException(Response::HTTP_NOT_FOUND, 'League not found!');
            }

            $entityManager->remove($league);
            $entityManager->flush();
        });
    }
}
