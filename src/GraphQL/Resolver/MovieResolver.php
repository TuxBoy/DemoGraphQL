<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\ArgumentInterface;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

/**
 * MovieResolver.
 */
class MovieResolver extends ResolverMap
{

	private MovieRepository $repository;

	private EntityManagerInterface $em;

	public function __construct(MovieRepository $repository, EntityManagerInterface $em)
	{
		$this->repository = $repository;
		$this->em         = $em;
	}

	/**
	 * @inheritDoc
	 */
	protected function map(): array
	{
		return [
			'Query' => [
				'movies' => fn($value, ArgumentInterface $args, \ArrayObject $context, ResolveInfo $info)
					=> $this->repository->findBy([], null, $args['limit'] ?? 10),
				'movie' => fn($value, ArgumentInterface $args, \ArrayObject $context, ResolveInfo $info)
					=> $this->repository->find($args['id'])
			],
			'Mutation' => [
				'updateMovie' => function ($value, ArgumentInterface $args, \ArrayObject $context, ResolveInfo $info) {
					$movie = $this->repository->find($args['id']);
					$movie->setTitle($args['title']);
					$this->em->persist($movie);
					$this->em->flush();

					return $movie;
				}
			]
		];
	}
}
