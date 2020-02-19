<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Director;
use App\Entity\Movie;
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

	public function __construct(AllocineService $allocine, EntityManagerInterface $entityManager)
	{
		parent::__construct(self::$defaultName);
		$this->allocine      = $allocine;
		$this->entityManager = $entityManager;
	}

	protected function configure(): void
	{
		$this
			->addArgument('count', InputArgument::OPTIONAL, 'Max count movies per page')
			->addArgument('page', InputArgument::OPTIONAL, 'Page movie number');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$count  = (int) $input->getArgument('count') ?? 1;
		$page   = (int) $input->getArgument('page') ?? 10;
		$movies = $this->allocine->movies($count, $page);

		foreach ($movies as $value) {
			$director = new Director();
			$director->setName($value['castingShort']['directors']);

			$this->entityManager->persist($director);

			$movie = new Movie();
			$movie->setTitle($value['title'])
				->setAllocineId($value['code'] ?? null)
				->setSynopsis($value['synopsisShort'])
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

		$this->entityManager->flush();
		$output->writeln(sprintf('Import de %s films ok', \count($movies)));

		return 0;
	}

}
