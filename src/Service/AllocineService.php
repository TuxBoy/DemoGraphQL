<?php

declare(strict_types=1);

namespace App\Service;

use AlloHelper;

class AllocineService
{

	private AlloHelper $alloHelper;

	public function __construct()
	{
		$this->alloHelper = new AlloHelper;
	}

	public function movies(int $count = 10, int $page = 1): array
	{
		return $this->alloHelper
				->movielist(['nowshowing'], ['dateasc'], $count, $page)
				->getArray()['movie'] ?? [];
	}

}
