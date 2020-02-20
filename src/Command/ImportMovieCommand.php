<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Director;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Service\AllocineService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportMovieCommand extends Command
{

	protected static $defaultName = 'movie:import';

	private AllocineService $allocine;

	private EntityManagerInterface $entityManager;

	private MovieRepository $movieRepository;

	public function __construct(
		AllocineService $allocine,
		EntityManagerInterface $entityManager,
		MovieRepository $movieRepository
	) {
		parent::__construct(self::$defaultName);
		$this->allocine      = $allocine;
		$this->entityManager = $entityManager;
		$this->movieRepository = $movieRepository;
	}

	protected function configure(): void
	{
		$this
			->addArgument('count', InputArgument::OPTIONAL, 'Max count movies per page')
			->addArgument('page', InputArgument::OPTIONAL, 'Page movie number');

		$this->addOption('truncate', 't', InputOption::VALUE_NONE, 'Vide la base de donnée si renseigné');
	}

	/**
	 * @param string $tableName
	 * @throws \Doctrine\DBAL\DBALException
	 */
	protected function truncate(string $tableName): void
	{
		$connection = $this->entityManager->getConnection();
		$platform   = $connection->getDatabasePlatform();
		$connection->exec($platform->getTruncateTableSQL($tableName, true));
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$total  = 0;
		$count  = $input->getArgument('count') ?? 30;
		$page   = $input->getArgument('page') ?? 1;
		$movies = $this->allocine->movies((int) $count, (int)$page);

		if ($input->getOption('truncate')) {
			$this->truncate('movie');
		}

		foreach ($movies as $value) {
			$movie      = new Movie;
			/*$movieExist = $this->movieRepository->findBy(['title' => $value['title']]);
			if ($movieExist) {
				continue;
			}*/
			$castingShort = $value['castingShort'] ?? null;
			if (isset($castingShort['directors'])) {
				$director = new Director();
				$director->setName($castingShort['directors']);

				$this->entityManager->persist($director);
				$movie->setDirector($director);
			}

			$movie
				->setTitle($value['title'])
				->setAllocineId($value['code'] ?? null)
				->setSynopsis($value['synopsisShort'] ?? '')
				->setPoster($value['poster']['href'] ?? null);

			if ($castingShort && isset($castingShort['actors'])) {
				$actors = array_map(
					fn ($actor) => $actor, explode(', ', $castingShort['actors'])
				);
				foreach ($actors as $v) {
					$actor = new Actor();
					$actor->setName($v);

					$this->entityManager->persist($actor);
					$movie->addActor($actor);
				}
			}
			$this->entityManager->persist($movie);
			$total++;
		}

		$this->entityManager->flush();
		$output->writeln(sprintf('Import de %s films ok', $total));

		return 0;
	}

}
