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

use App\Controller\Admin\UserCrudController;
use App\Document\DocumentInterface;
use App\Entity\EntityInterface;
use App\Entity\User;
use Doctrine\ORM\Exception\NotSupported;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

/**
 * Class UserCrudControllerFunctionalTest - for all functional tests
 * in UserCrudController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 101 - Business process - User
 * @link https://www.atlassian.com/software/confluence/bp/101
 *
 * @package App\Tests\Functional\Admin
 */
class UserCrudControllerFunctionalTest extends BaseCrudControllerFunctional
{
    public const TEXT_USERS = 'Users';
    public const TEXT_CREATE_USER = 'Create User';
    public const TEXT_EDIT_USER = 'Edit User';
    public const TEXT_USER_DETAILS = 'User details';

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return UserCrudController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return User::class;
    }

    /**
     * @testCase 1039 - Functional test actions for UserCrudController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1039
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2039 - For UserCrudController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2039
     * @bp 101 - Business process - User
     * @link https://www.atlassian.com/software/confluence/bp/101
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdminUserCrud(): void
    {
        $this->debugFunction(self::class, 'testAdminUserCrud');

        $this->act1LoginGet();
        $this->act2LoginPost();
        $this->act3AdminGet();
        $this->actIndexGet('4', self::TEXT_USERS, 'UserCrudController');
        $this->act5DetailGet(User::class, 'UserCrudController', self::TEXT_USER_DETAILS);
        $this->act6EditGet(self::TEXT_EDIT_USER, 'UserCrudController', self::TEXT_USER_DETAILS);
        $this->act7EditPost(User::class, 'UserCrudController');
        $this->act8DetailGet($this->entity->getUsername(), 'UserCrudController', self::TEXT_USER_DETAILS);
        $this->actIndexGet('9', self::TEXT_USERS, 'UserCrudController');
        $this->act10NewGetSubmit(self::TEXT_CREATE_USER, 'UserCrudController', self::TEXT_USER_DETAILS);
        $this->actIndexGet('11', self::TEXT_USERS, 'UserCrudController');
        $this->act12DeleteGet('UserCrudController');
        $this->act13DeleteDelete('UserCrudController');
        $this->actIndexGet('14', self::TEXT_USERS, 'UserCrudController');
        $this->act15LogoutGet();
        $this->act16GetEntityFqcn(
            UserCrudController::class,
            'UserCrudController',
            User::class
        );
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        return $this->entity->getUsername();
    }

    /**
     * @param EntityInterface|DocumentInterface $user
     *
     * @return void
     * @throws NotSupported
     */
    protected function assertByDbNew(EntityInterface|DocumentInterface $user): void
    {
        if ($this->assertByDb) {
            $this->userByUsername($this->entity->getUsername());
            /** @var User $user */
            $this->assertEquals($user->getUsername(), $this->user->getUsername());
            $this->assertEquals($user->getFullName(), $this->user->getFullName());
            $this->assertEquals($user->getGender(), $this->user->getGender());
            $this->assertEquals($user->getEmail(), $this->user->getEmail());
            $this->assertEquals($user->getDescription(), $this->user->getDescription());
            $this->assertEquals($user->getStatus(), $this->user->getStatus());
            $this->assertEquals($user->getAccess(), $this->user->getAccess());
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
            $this->assertEquals($param['username'], $this->user->getUsername());
            $this->assertEquals($param['fullName'], $this->user->getFullName());
        }
    }

    /**
     * @return void
     */
    protected function assertByDbDelete(): void
    {
        if ($this->assertByDb) {
            $this->assertNull($this->user);
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
        $user = $this->createUser();
        $formNew = $crawler->selectButton('Create')->form();
        $formNew['User[uid]'] = $user->getUid();
        $formNew['User[username]'] = $user->getUsername();
        $formNew['User[fullName]'] = $user->getFullName();
        $formNew['User[gender]'] = $user->getGender();
        $formNew['User[email]'] = $user->getEmail();
        $formNew['User[description]'] = $user->getDescription();
        $formNew['User[status]'] = $user->getStatus();
        $formNew['User[access]'] = $user->getAccess();
        $formNew['User[rolePermissions]'] = $user->getRolePermissionsArray();
        $formNew['User[dateBirthday]'] = $user->getDateBirthday()->format('Y-m-d');

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
        $id = (int)$formEdit['User[id]']->getValue();
        $this->assertEquals($this->entityId, $id);
        $param = [];
        $param['username'] = $formEdit['User[username]']->getValue() . '000';
        $formEdit['User[username]']->setValue($param['username']);
        $param['fullName'] = $formEdit['User[fullName]']->getValue() . ' 000';
        $formEdit['User[fullName]']->setValue($param['fullName']);

        return [$formEdit, $param];
    }
}
