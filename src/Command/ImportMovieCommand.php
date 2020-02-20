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
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$count  = $input->getArgument('count') ?? 30;
		$page   = $input->getArgument('page') ?? 1;
		$movies = $this->allocine->movies((int) $count, (int)$page);

		foreach ($movies as $value) {
			if (isset($value['castingShort']) && !empty($value['castingShort']['actors'])) {
				$movieExist = $this->movieRepository->findBy(['title' => $value['title']]);
				if ($movieExist) {
					continue;
				}
				$castingShort = $value['castingShort'];
				$director = new Director();
				$director->setName($castingShort['directors']);

				$this->entityManager->persist($director);

				$movie = new Movie();
				$movie->setTitle($value['title'])
					->setAllocineId($value['code'] ?? null)
					->setSynopsis($value['synopsisShort'] ?? '')
					->setPoster($value['poster']['href'] ?? null)
					->setDirector($director);

				$actors = array_map(fn ($actor) => $actor, explode(', ', $value['castingShort']['actors']));

				foreach ($actors as $v) {
					$actor = new Actor();
					$actor->setName($v);

					$this->entityManager->persist($actor);
					$movie->addActor($actor);
				}
				$this->entityManager->persist($movie);
			}
		}

		$this->entityManager->flush();
		$output->writeln(sprintf('Import de %s films ok', \count($movies)));

		return 0;
	}

}
