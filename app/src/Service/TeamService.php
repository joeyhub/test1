<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TeamRepository;
use App\Entity\Team;
use App\Entity\League;
use App\Model\Strip;

class TeamService extends DoctrineService
{
    private function getRepository(): TeamRepository
    {
        return $this->entityManager->getRepository(Team::class);
    }

    private function persistTeam(?int $id, string $name, Strip $strip, int $league): Team
    {
        $team = null;

        $this->entityManager->transactional(function (EntityManagerInterface $entityManager) use ($id, $name, $strip, $league, &$team): void {
            if (null === $id) {
                $team = new Team();
            } else {
                $team = $this->getRepository()->find($id);

                if (null === $team) {
                    throw new HttpException(Response::HTTP_NOT_FOUND, 'Team not found!');
                }
            }

            $league = $entityManager->getRepository(League::class)->find($league);

            if (null === $league) {
                throw new HttpException(Response::HTTP_CONFLICT, 'League not found!');
            }

            // Note: It falls onto the database layer to produce a not unique exception.
            $team->setName($name);
            // Note: It might be more efficient to be able to set the league without reading it as long as the database layer supports foreign keys.
            $team->setLeague($league);

            $entityManager->persist($team);
            $entityManager->flush();
        });

        return $team;
    }

    public function create(string $name, Strip $strip, int $league): int
    {
        return $this->persistTeam(null, $name, $strip, $league)->getId();
    }

    public function update(int $id, string $name, Strip $strip, int $league): void
    {
        $this->persistTeam($id, $name, $strip, $league);
    }

    public function findByLeague(int $league): array
    {
        // Note: No check here that the league exists!
        return $this->getRepository()->findByLeague($league);
    }
}
