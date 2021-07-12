<?php

namespace App\Command;

use Aln\Speedtest\Speedtest;
use App\Entity\Logger;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LaunchCommand extends Command
{
    protected static $defaultName = 'app:launch';
    protected static $defaultDescription = 'Launch command speedtest';

    private $manager;
    private $logger;

    public function __construct(string $name = null, EntityManagerInterface $manager, LoggerInterface $logger)
    {
        parent::__construct($name);
        $this->manager = $manager;
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Launch Command speedtest');

        $this->manager->beginTransaction();
        try {
            $speedTest = new Speedtest();
            $speedTest->getServers();
            $speedTest->getBestServer();
            $speedTest->download();
            $speedTest->upload();
            $results = $speedTest->results();

            $io->note('Speedtest finish');

            $logger = new Logger();
            $logger->setUpload($results->getUpload())
                ->setDownload($results->getDownload())
                ->setLatency($results->getLatency())
                ->setBytes(['receive' => $results->getBytesReceived(), 'sent' => $results->getBytesSent()])
                ->setLaunchedAt(new DateTimeImmutable())
            ;

            $this->manager->persist($logger);
            $this->manager->flush();

            $this->manager->commit();
            $io->success('Speedtest finish.');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->manager->rollback();
            $this->logger->critical($e->getMessage(), ['commandLaunch']);
            $io->error(sprintf('Speedtest error : %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
