<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\League;
use App\Entity\Team;
use App\Model\Strip;
use App\Model\Colour;

class FootballFixtures extends Fixture
{
    // Note: This would be better off in a data file. Could even have a fetch system.
    // Source: https://github.com/openfootball/
    const LEAGUES = [[
        'name' => 'English Premier League 2017/18',
        'teams' => [
            'Chelsea', 'Arsenal', 'Tottenham Hotspur', 'West Ham United', 'Crystal Palace', 'Manchester United',
            'Manchester City', 'Everton', 'Liverpool', 'West Bromwich Albion', 'Newcastle United', 'Stoke City',
            'Southampton', 'Leicester City', 'Bournemouth', 'Watford', 'Brighton & Hove Albion', 'Burnley',
            'Huddersfield Town', 'Swansea',
        ],
    ]];

    public function load(ObjectManager $manager)
    {
        $color = 0;

        foreach (self::LEAGUES as $data) {
            $league = new League();
            $league->setName($data['name']);

            $manager->persist($league);

            foreach ($data['teams'] as $name) {
                $strip = new Strip();
                $strip->addColour(Colour::getByName(Colour::COLOURS[$color++ % count(Colour::COLOURS)]));

                $team = new Team();
                $team->setName($name)->setStrip($strip)->setLeague($league);

                $manager->persist($team);
            }
        }

        $manager->flush();
    }
}
