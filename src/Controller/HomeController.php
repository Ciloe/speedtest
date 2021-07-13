<?php

namespace App\Controller;

use App\Repository\LoggerRepository;
use App\Service\LoggerChart;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, LoggerRepository $repository, LoggerChart $service): Response
    {
        $startedAt = $request->get('started_at');
        $endedAt = $request->get('ended_at');

        return $this->render('home/index.html.twig', [
            'chart' => $service->getHomeChart(
                $repository->findLatest(
                    !is_null($startedAt) ? new DateTimeImmutable($startedAt) : null,
                    !is_null($endedAt) ? new DateTimeImmutable($endedAt) : null
                )
            ),
        ]);
    }
}
