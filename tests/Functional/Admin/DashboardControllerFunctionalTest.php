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

use App\Controller\Admin\DashboardController;
use App\Entity\EntityInterface;
use Exception;

/**
 * Class DashboardControllerFunctionalTest - for all functional tests
 * in DashboardController with Auth and without Auth
 * with real connecting to external services, such as databases, message brokers, etc.
 *
 * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
 * @bp 105 - Business process - Dashboard
 * @link https://www.atlassian.com/software/confluence/bp/105
 *
 * @package App\Tests\Functional\Admin
 */
class DashboardControllerFunctionalTest extends BaseCrudControllerFunctional
{
    public const TEXT_DASHBOARD = 'Dashboard';
    public const TEXT_DASHBOARD_DETAILS = 'Welcome to Admin application';

    /**
     * @return string
     */
    protected function getControllerFqcn(): string
    {
        return DashboardController::class;
    }

    /**
     * @return string
     */
    protected static function getEntityName(): string
    {
        return EntityInterface::class;
    }

    /**
     * @testCase 1041 - Functional test actions for DashboardController without AUTH - must be a success
     * @link https://www.testrail.com/testCase/1041
     * @author QA Dmytro Petrenko <petrenko_d@gmail.com>
     * @task 2041 - For DashboardController actions without AUTH - must be a success
     * @link https://www.atlassian.com/ru/software/jira/task/2041
     * @bp 105 - Business process - Dashboard
     * @link https://www.atlassian.com/software/confluence/bp/105
     *     Arrange:
     * without AUTH
     *
     * @return void
     * @throws Exception
     */
    public function testAdminDashboard(): void
    {
        $this->debugFunction(self::class, 'testAdminDashboard');

        $this->act1LoginGet();
        $this->act2LoginPost();
        $this->act3AdminGet();
        $this->actDashboardGet(self::TEXT_DASHBOARD, self::TEXT_DASHBOARD_DETAILS);
        $this->act15LogoutGet();
    }
}
