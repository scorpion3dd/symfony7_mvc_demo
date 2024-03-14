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

use App\Controller\Admin\LogController;
use App\Document\DocumentInterface;
use App\Document\Log;
use App\Entity\EntityInterface;
use Doctrine\ORM\Exception\NotSupported;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\InvalidClassPropertyTypeException;
use EasyCorp\Bundle\EasyAdminBundle\Test\Exception\MissingClassMethodException;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogControllerFunctionalTest - for all functional tests
 * in LogController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 106 - Business process - Log
 * @link https://www.atlassian.com/software/confluence/bp/106
 *
 * @package App\Tests\Functional\Admin
 */
class LogControllerFunctionalTest extends BaseCrudControllerFunctional
{
    public const TEXT_LOGS = 'Logs';
    public const TEXT_CREATE_LOG = 'Create Log';
    public const TEXT_EDIT_LOG = 'Edit Log';

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return LogController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return Log::class;
    }

    /**
     * @testCase 1044 - Functional test actions for LogController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1044
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2044 - For LogController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2044
     * @bp 106 - Business process - Log
     * @link https://www.atlassian.com/software/confluence/bp/106
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdminLogCrud(): void
    {
        $this->debugFunction(self::class, 'testAdminLogCrud');

        $this->act1LoginGet();
        $this->act2LoginPost();
        $this->act3AdminGet();
        $this->actIndexGet('4', self::TEXT_LOGS, 'LogController', true);
        $this->act5DetailGet(Log::class, 'LogController', null, true);
        $this->act6EditGet(self::TEXT_EDIT_LOG, 'LogController');
        $this->act6EditGetEmptyLog();
        $this->act7EditPost(Log::class, 'LogController');
        $this->act7EditPostFormErrors();
        $this->act8DetailGet($this->entity->getId(), 'LogController', null, true);
        $this->actIndexGet('9', self::TEXT_LOGS, 'LogController', true);
        $this->act10NewGetSubmit(self::TEXT_CREATE_LOG, 'LogController');
        $this->act10NewPostFormErrors();
        $this->actIndexGet('11', self::TEXT_LOGS, 'LogController', true);
        $this->act12DeleteGet('LogController');
        $this->act13DeleteDelete('LogController', true);
        $this->actIndexGet('14', self::TEXT_LOGS, 'LogController', true);
        $this->act15LogoutGet();
    }

    /**
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function act6EditGetEmptyLog(): void
    {
        $this->adminContext('Log');
        $this->debugFunction(self::class, 'Act 6: Get Edit empty Log - '
            . 'GET /en/admin?routeName=log_edit&id=123456789');
        $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_edit&id=123456789'),
            $this->getCrudPostParametersLogEdit()
        );
        static::assertResponseRedirects();
    }

    /**
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function act7EditPostFormErrors(): void
    {
        $this->adminContext('Log');
        $this->debugFunction(self::class, 'Act 7: Post Edit form errors - '
            . 'GET /en/admin?routeName=log_edit&id=65a31f12da81997f160c61f2');
        $id = isset($this->log) ? $this->log->getId() : $this->entityId;
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_edit&id=' . $id),
            $this->getCrudPostParametersLogEdit()
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Edit Log', $html);
    }

    /**
     * @return void
     * @throws InvalidClassPropertyTypeException
     * @throws MissingClassMethodException
     */
    protected function act10NewPostFormErrors(): void
    {
        $this->adminContext('Log');
        $this->debugFunction(self::class, 'Act 10: Post New form errors - '
            . 'GET /en/admin?routeName=log_add');
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->getCrudUrlLogRouteName('routeName=log_add'),
            $this->getCrudPostParametersLogEdit()
        );
        $html = $crawler->html();
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('Create Log', $html);
    }

    /**
     * @return string
     */
    protected function getTitle(): string
    {
        return $this->entity->getId();
    }

    /**
     * @param EntityInterface|DocumentInterface $log
     *
     * @return void
     * @throws NotSupported
     */
    protected function assertByDbNew(EntityInterface|DocumentInterface $log): void
    {
        if ($this->assertByDb) {
            $this->logByMessage($this->entity->getMessage());
            /** @var Log $log */
            $this->assertEquals($log->getMessage(), $this->log->getMessage());
            $this->assertEquals($log->getPriority(), $this->log->getPriority());
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
            $this->assertEquals($param['message'], $this->log->getMessage());
        }
    }

    /**
     * @return void
     */
    protected function assertByDbDelete(): void
    {
        if ($this->assertByDb) {
            $this->assertNull($this->log);
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
        $log = $this->createLog();
        $log->setMessage($log->getMessage() . ' same');
        $formNew = $crawler->selectButton('Save changes')->form();
        $formNew['log_form[message]'] = $log->getMessage();
        $formNew['log_form[priority]'] = $log->getPriority();

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
        $id = (string)$formEdit['log_form[id]']->getValue();
        $this->assertEquals($this->entityId, $id);
        $param = [];
        $param['message'] = $formEdit['log_form[message]']->getValue() . ' 000';
        $formEdit['log_form[message]']->setValue($param['message']);

        return [$formEdit, $param];
    }
}
