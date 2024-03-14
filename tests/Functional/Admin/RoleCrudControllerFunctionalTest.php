<?php
/**
 * This file is part of the Simple Web Demo Free Admin Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use App\Controller\Admin\RoleCrudController;
use App\Document\DocumentInterface;
use App\Entity\EntityInterface;
use App\Entity\Role;
use Doctrine\ORM\Exception\NotSupported;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * Class RoleCrudControllerFunctionalTest - for all functional tests
 * in RoleCrudController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 102 - Business process - Role
 * @link https://www.atlassian.com/software/confluence/bp/102
 *
 * @package App\Tests\Functional\Admin
 */
class RoleCrudControllerFunctionalTest extends BaseCrudControllerFunctional
{
    public const TEXT_ROLES = 'Roles';
    public const TEXT_CREATE_ROLE = 'Create Role';
    public const TEXT_EDIT_ROLE = 'Edit Role';

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return RoleCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Role::class;
    }

    /**
     * @testCase 1042 - Functional test actions for RoleCrudController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1042
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2042 - For RoleCrudController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2042
     * @bp 102 - Business process - Role
     * @link https://www.atlassian.com/software/confluence/bp/102
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdminRoleCrud(): void
    {
        $this->debugFunction(self::class, 'testAdminRoleCrud');

        $this->act1LoginGet();
        $this->act2LoginPost();
        $this->act3AdminGet();
        $this->actIndexGet('4', self::TEXT_ROLES, 'RoleCrudController');
        $this->act5DetailGet(Role::class, 'RoleCrudController');
        $this->act6EditGet(self::TEXT_EDIT_ROLE, 'RoleCrudController');
        $this->act7EditPost(Role::class, 'RoleCrudController');
        $this->act8DetailGet($this->entity->getName(), 'RoleCrudController');
        $this->actIndexGet('9', self::TEXT_ROLES, 'RoleCrudController');
        $this->act10NewGetSubmit(self::TEXT_CREATE_ROLE, 'RoleCrudController');
        $this->actIndexGet('11', self::TEXT_ROLES, 'RoleCrudController');
        $this->act12DeleteGet('RoleCrudController');
        $this->act13DeleteDelete('RoleCrudController');
        $this->actIndexGet('14', self::TEXT_ROLES, 'RoleCrudController');
        $this->act15LogoutGet();
        $this->act16GetEntityFqcn(
            RoleCrudController::class,
            'RoleCrudController',
            Role::class
        );
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        return $this->entity->getName();
    }

    /**
     * @param EntityInterface|DocumentInterface $role
     *
     * @return void
     * @throws NotSupported
     */
    protected function assertByDbNew(EntityInterface|DocumentInterface $role): void
    {
        if ($this->assertByDb) {
            $this->roleByName($this->entity->getName());
            /** @var Role $role */
            $this->assertEquals($role->getName(), $this->role->getName());
            $this->assertEquals($role->getDescription(), $this->role->getDescription());
        }
    }

    /**
     * @param array $param
     *
     * @return void
     */
    protected function assertByDbEdit(array $param): void
    {
        if ($this->assertByDb) {
            $this->assertEquals($param['name'], $this->role->getName());
        }
    }

    /**
     * @return void
     */
    protected function assertByDbDelete(): void
    {
        if ($this->assertByDb) {
            $this->assertNull($this->role);
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return Form
     * @throws Exception
     */
    protected function getFormNew(Crawler $crawler): Form
    {
        $role = $this->createRole();
        $role->setName($role->getName() . ' same');
        $formNew = $crawler->selectButton('Create')->form();
        $formNew['Role[name]'] = $role->getName();
        $formNew['Role[description]'] = $role->getDescription();
        $formNew['Role[permissions]'] = $role->getPermissionsArray();
        $formNew['Role[parentRoles]'] = [];
        $formNew['Role[childRoles]'] = [];

        return $formNew;
    }

    /**
     * @param Crawler $crawler
     *
     * @return array
     */
    protected function getFormEdit(Crawler $crawler): array
    {
        $formEdit = $crawler->selectButton('Save changes')->form();
        $id = (int)$formEdit['Role[id]']->getValue();
        $this->assertEquals($this->entityId, $id);
        $param = [];
        $param['name'] = $formEdit['Role[name]']->getValue() . '000';
        $formEdit['Role[name]']->setValue($param['name']);
        $param['description'] = $formEdit['Role[description]']->getValue() . ' 000';
        $formEdit['Role[description]']->setValue($param['description']);

        return [$formEdit, $param];
    }
}
