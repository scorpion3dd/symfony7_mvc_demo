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

namespace App\Helper;

use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * Class UserChartHelper
 * @package App\Helper
 */
class UserChartHelper
{
    /**
     * @param TranslatorInterface $translator
     * @param ChartBuilderInterface $chartBuilder
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ChartBuilderInterface $chartBuilder,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return Chart
     */
    public function createChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => $this->translator->trans('New comments'),
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
                [
                    'label' => $this->translator->trans('Accessed comments'),
                    'backgroundColor' => 'rgb(132, 99, 132)',
                    'borderColor' => 'rgb(132, 99, 132)',
                    'data' => [0, 8, 3, 1, 18, 27, 43],
                ],
            ],
        ]);
        $chart->setOptions([
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => $this->translator->trans('Comments'),
                ],
                'legend' => [
                    'position' => 'bottom',
                ],
                'zoom' => [
                    'zoom' => [
                        'wheel' => ['enabled' => true],
                        'pinch' => ['enabled' => true],
                        'mode' => 'xy',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);

        return $chart;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDataChartUsers(): string
    {
        $dataChartUsers = '';
        $users = $this->userRepository->findUsersAccessed();
        if (count($users) > 0) {
            foreach ($users as $user) {
                $countAccessedUsers = $user['countUpdatedAt'];
                if ($user['countUpdatedAt'] == $user['countCreatedAt']) {
                    $delta = random_int(0, 10);
                    $countAccessedUsers = ($user['countCreatedAt'] - $delta) < 0 ? 1 : $user['countCreatedAt'] - $delta;
                }
                $dataChartUsers .= $this->itemDataChartUsers($user['yearAt'], $user['countCreatedAt'], $countAccessedUsers);
            }
        } else {
            $dataChartUsers = $this->getDataChartUsersRandom();
        }

        return $dataChartUsers;
    }

    /**
     * https://www.chartjs.org/docs/latest/getting-started/usage.html
     * https://www.chartjs.org/docs/latest/samples/bar/border-radius.html
     *
     * @param string $dataChartUsers
     *
     * @return string
     */
    public function getJavascript(string $dataChartUsers = ''): string
    {
        $title = $this->translator->trans('Users');
        $label1 = $this->translator->trans('New users');
        $label2 = $this->translator->trans('Accessed users');

        $javascript = <<<END
<script type="text/javascript">
    console.log("DashboardController configureAssets javascript - start");
    
    document.addEventListener('Chart:init', function (event) {
        console.log('EventListener Chart:init - added');
        console.log(event);
    
    });
    
    (async function() {
      const data_chart_users = [$dataChartUsers];
    
      new Chart(
        document.getElementById('chart_users'),
        {
          type: 'bar',
          data: {
            labels: data_chart_users.map(row => row.year),
            datasets: [
              {
                label: '$label1',
                data: data_chart_users.map(row => row.count_new_users),
                borderWidth: 1
              },
              {
                label: '$label2',
                data: data_chart_users.map(row => row.count_accessed_users),
                borderWidth: 1
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'bottom',
              },
              title: {
                display: true,
                text: '$title'
              }
            }
          }
        }
      );
   })();
</script>
END;

        return $javascript;
    }

    /**
     * @param UserRepository $userRepository
     */
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getDataChartUsersRandom(): string
    {
        $dataChartUsers = '';
        $countYear = 10;
        $year = 2000;
        for ($i = 1; $i <= $countYear; $i++) {
            $countNewUsers = random_int(0, 100);
            $deltaUsers = random_int(0, 10);
            $countAccessedUsers = ($countNewUsers - $deltaUsers) < 0 ? 0 : $countNewUsers - $deltaUsers;
            $dataChartUsers .= $this->itemDataChartUsers($year, $countNewUsers, $countAccessedUsers);
            $year++;
        }

        return $dataChartUsers;
    }

    /**
     * @param int $year
     * @param int $countNew
     * @param int $countAccessed
     *
     * @return string
     */
    private function itemDataChartUsers(int $year, int $countNew, int $countAccessed): string
    {
        return '{ year: ' . $year
            . ', count_new_users: ' . $countNew
            . ', count_accessed_users: ' . $countAccessed . ' },';
    }
}
