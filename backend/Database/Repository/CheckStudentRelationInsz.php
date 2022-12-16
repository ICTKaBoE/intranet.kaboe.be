<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Input;

class CheckStudentRelationInsz extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_check_student_relation_insz", \Database\Object\CheckStudentRelationInsz::class, orderField: 'insertDateTime');
	}

	public function getByInsz($insz)
	{
		$items = $this->get();
		return Arrays::firstOrNull(Arrays::filter($items, fn ($i) => Strings::equal($i->childInsz, Input::formatInsz($insz))));
	}
}
