<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\ArgumentInterface;

/**
 * Resolver.
 */
interface ResolverInterface
{

	public function __invoke($value, ArgumentInterface $args, \ArrayObject $context, ResolveInfo $info);

}
