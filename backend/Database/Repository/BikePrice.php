<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;

class BikePrice extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_bike_price", \Database\Object\BikePrice::class, orderField: 'validFrom');
	}

	public function getBetween($date)
	{
		$items = $this->get();
		return Arrays::first(Arrays::filter($items, fn ($i) => Clock::at($date)->isAfterOrEqualTo(Clock::at($i->validFrom)) && Clock::at($date)->isBeforeOrEqualTo(Clock::at($i->validUntil))));
	}
}
