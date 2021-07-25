<?php

namespace App\DataFixtures\Providers;

class UserProvider
{
    /**
     * Contains a collection of users
     *
     * @var array
     */
    private const USERS = [
        [
            'username'      => 'Servietsky',
            'plainPassword' => 'Ylq?12$PeviTYdm8',
            'roles'         => [
                'ROLE_USER',
            ],
        ],
        [
            'username'      => 'PolarBear',
            'plainPassword' => 'FM$V&YN2i2yCK@ul',
            'roles'         => [
                'ROLE_USER',
            ],
        ],
    ];

    public static function getUsers(): array
    {
        return self::USERS;
    }
}
