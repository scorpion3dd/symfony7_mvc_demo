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

namespace App\Service;

use App\Entity\Admin;
use Doctrine\ORM\NonUniqueResultException;

/**
 * Interface AdminServiceInterface
 * @package App\Service
 */
interface AdminServiceInterface extends BaseServiceInterface
{
    /**
     * @param int $adminId
     *
     * @return Admin|null
     */
    public function findOneById(int $adminId): ?Admin;

    /**
     * @param string $username
     *
     * @return Admin|null
     * @throws NonUniqueResultException
     */
    public function findOneByLogin(string $username): ?Admin;

    /**
     * @return array|null
     */
    public function findAll(): ?array;
}
