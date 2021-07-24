<?php

namespace App\DataFixtures\Providers;

class SportProvider
{
    /**
     * Contains a collection of sport names
     *
     * @var array
     */
    private const SPORTS = [
        'Parkour',
        'Aerobatics',
        'Parachuting',
        'Paragliding',
        'Paramotoring',
        'Flight archery',
        'Badminton',
        'Volleyball',
        'Table tennis',
        'Basketball',
        'Baseball',
        'Cricket',
        'Skateboarding',
        'Snowboarding',
        'Surfing',
        'Dodgeball',
        'Rock climbing',
        'BMX',
        'Judo',
        'Sumo',
        'Wrestling',
        'Boxing',
        'Football',
        'Golf',
        'Acrobatic gymnastics',
        'Trampolining',
        'Kitesurfing',
        'Triathlon',
        'Tennis',
        'Bobsleigh',
        'Luge',
        'Airsoft',
        'Paintball',
        'Laser tag',
        'Hockey',
    ];

    public static function getSports(): array
    {
        return static::SPORTS;
    }
}
