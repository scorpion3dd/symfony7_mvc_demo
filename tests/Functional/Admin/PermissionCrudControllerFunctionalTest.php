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

use App\Controller\Admin\PermissionCrudController;
use App\Document\DocumentInterface;
use App\Entity\EntityInterface;
use App\Entity\Permission;
use Doctrine\ORM\Exception\NotSupported;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * Class PermissionCrudControllerFunctionalTest - for all functional tests
 * in PermissionCrudController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 103 - Business process - Permission
 * @link https://www.atlassian.com/software/confluence/bp/103
 *
 * @package App\Tests\Functional\Admin
 */
class PermissionCrudControllerFunctionalTest extends BaseCrudControllerFunctional
{
    public const TEXT_PERMISSIONS = 'Permissions';
    public const TEXT_CREATE_PERMISSION = 'Create Permission';
    public const TEXT_EDIT_PERMISSION = 'Edit Permission';

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return PermissionCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Permission::class;
    }

    /**
     * @testCase 1043 - Functional test actions for PermissionCrudController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1043
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2043 - For PermissionCrudController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2043
     * @bp 103 - Business process - Permission
     * @link https://www.atlassian.com/software/confluence/bp/103
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdminPermissionCrud(): void
    {
        $this->debugFunction(self::class, 'testAdminPermissionCrud');

        $this->act1LoginGet();
        $this->act2LoginPost();
        $this->act3AdminGet();
        $this->actIndexGet('4', self::TEXT_PERMISSIONS, 'PermissionCrudController');
        $this->act5DetailGet(Permission::class, 'PermissionCrudController');
        $this->act6EditGet(self::TEXT_EDIT_PERMISSION, 'PermissionCrudController');
        $this->act7EditPost(Permission::class, 'PermissionCrudController');
        $this->act8DetailGet($this->entity->getName(), 'PermissionCrudController');
        $this->actIndexGet('9', self::TEXT_PERMISSIONS, 'PermissionCrudController');
        $this->act10NewGetSubmit(self::TEXT_CREATE_PERMISSION, 'PermissionCrudController');
        $this->actIndexGet('11', self::TEXT_PERMISSIONS, 'PermissionCrudController');
        $this->act12DeleteGet('PermissionCrudController');
        $this->act13DeleteDelete('PermissionCrudController');
        $this->actIndexGet('14', self::TEXT_PERMISSIONS, 'PermissionCrudController');
        $this->act15LogoutGet();
        $this->act16GetEntityFqcn(
            PermissionCrudController::class,
            'PermissionCrudController',
            Permission::class
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
     * @param EntityInterface|DocumentInterface $permission
     *
     * @return void
     * @throws NotSupported
     */
    protected function assertByDbNew(EntityInterface|DocumentInterface $permission): void
    {
        if ($this->assertByDb) {
            $this->permissionByName($this->entity->getName());
            /** @var Permission $permission */
            $this->assertEquals($permission->getName(), $this->permission->getName());
            $this->assertEquals($permission->getDescription(), $this->permission->getDescription());
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
            $this->assertEquals($param['name'], $this->permission->getName());
        }
    }

    /**
     * @return void
     */
    protected function assertByDbDelete(): void
    {
        if ($this->assertByDb) {
            $this->assertNull($this->permission);
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
        $permission = $this->createPermission();
        $permission->setName($permission->getName() . ' same');
        $formNew = $crawler->selectButton('Create')->form();
        $formNew['Permission[name]'] = $permission->getName();
        $formNew['Permission[description]'] = $permission->getDescription();
        $formNew['Permission[roles]'] = $permission->getRolesArray();

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
        $id = (int)$formEdit['Permission[id]']->getValue();
        $this->assertEquals($this->entityId, $id);
        $param = [];
        $param['name'] = $formEdit['Permission[name]']->getValue() . '000';
        $formEdit['Permission[name]']->setValue($param['name']);
        $param['description'] = $formEdit['Permission[description]']->getValue() . ' 000';
        $formEdit['Permission[description]']->setValue($param['description']);

        return [$formEdit, $param];
    }
}
