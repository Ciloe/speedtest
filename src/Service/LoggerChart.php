<?php

namespace App\Service;

use App\Entity\Logger;
use App\Repository\LoggerRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class LoggerChart
{
    private $chartBuilder;
    private $repository;

    public function __construct(ChartBuilderInterface $chartBuilder, LoggerRepository $repository)
    {
        $this->chartBuilder = $chartBuilder;
        $this->repository = $repository;
    }

    public function getHomeChart(array $logs): Chart
    {
        $labels = array_map(function (Logger $logger) {
            return $logger->getLaunchedAt()->format('Y-m-d H:i:s');
        }, $logs);

        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                $this->getData(
                    sprintf('Upload (%d Mbps)', round($this->repository->getAvgUpload() / 1000000, 2)),
                    array_map(function (Logger $logger) {
                        return round($logger->getUpload() / 1000000, 2);
                    }, $logs),
                    'rgb(255, 99, 132)'
                ),
                $this->getData(
                    sprintf('Download (%d Mbps)', round($this->repository->getAvgDownload() / 1000000, 2)),
                    array_map(function (Logger $logger) {
                        return round($logger->getDownload() / 1000000, 2);
                    }, $logs),
                    'rgb(200, 80, 132)'
                ),
                $this->getData(
                    sprintf('Latency (%d Mbps)', round($this->repository->getAvgLatency(), 2)),
                    array_map(function (Logger $logger) {
                        return $logger->getLatency();
                    }, $logs),
                    'rgb(40, 80, 100)'
                ),
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'yAxes' => [
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

    private function getData(string $title, array $data, string $color)
    {
        return [
            'label' => $title,
            'borderColor' => $color,
            'data' => $data,
        ];
    }
}
