<?php

namespace App\Controller;

use App\Entity\Logger;
use App\Repository\LoggerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class HomeController extends AbstractController
{
    private ChartBuilderInterface $chartBuilder;

    public function __construct(ChartBuilderInterface $chartBuilder)
    {
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(LoggerRepository $repository): Response
    {
        $logs = $repository->findLatest();
        $labels = array_map(function (Logger $logger) {
            return $logger->getLaunchedAt()->format('H:i');
        }, $logs);

        $chartUpload = $this->getGraph(
            'Upload',
            $labels,
            array_map(function (Logger $logger) {
                return round($logger->getUpload() / 1000000, 2);
            }, $logs),
            'rgb(255, 99, 132)',
        );
        $chartDownload = $this->getGraph(
            'Download',
            $labels,
            array_map(function (Logger $logger) {
                return round($logger->getDownload() / 1000000, 2);
            }, $logs),
            'rgb(200, 80, 132)',
        );
        $chartLatency = $this->getGraph(
            'Latency',
            $labels,
            array_map(function (Logger $logger) {
                return $logger->getLatency();
            }, $logs),
            'rgb(40, 80, 100)',
        );

        return $this->render('home/index.html.twig', [
            'chart_upload' => $chartUpload,
            'chart_download' => $chartDownload,
            'chart_latency' => $chartLatency,
        ]);
    }

    private function getGraph(string $title, array $labels, array $data, string $color)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $title,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'min' => 0,
                    'max' => 300,
                    'ticks' => [
                        'stepSize' => 50,
                    ]
                ],
            ],
        ]);

        return $chart;
    }
}
