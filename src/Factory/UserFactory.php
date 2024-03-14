<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Enum\Roles;
use DateTime;

/**
 * Class UserFactory - is the Factory Method design pattern.
 *
 * @link https://designpatternsphp.readthedocs.io/en/latest/Creational/FactoryMethod/README.html
 * @package App\Factory
 */
class UserFactory
{
    public function __construct()
    {
    }

    /**
     * @param string $email
     * @param int $gender
     * @param string $username
     * @param string $fullName
     * @param string $description
     * @param int $statusId
     * @param int $accessId
     * @param DateTime $dateBirthday
     * @param DateTime $createdAt
     * @param DateTime $updatedAt
     *
     * @return User
     */
    public function create(
        string $email,
        int $gender,
        string $username,
        string $fullName,
        string $description,
        int $statusId,
        int $accessId,
        DateTime $dateBirthday,
        DateTime $createdAt,
        DateTime $updatedAt,
    ): User {
        $user = new User();
        $user->setRoles([Roles::ROLE_USER]);
        $user->setGender($gender);
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setFullName($fullName);
        $user->setDescription($description);
        $user->setStatus($statusId);
        $user->setAccess($accessId);
        $slug = $user->buildSlug();
        $user->setSlug($slug);
        $user->setDateBirthday($dateBirthday);
        $user->setCreatedAt($createdAt);
        $user->setUpdatedAt($updatedAt);

        return $user;
    }
}
