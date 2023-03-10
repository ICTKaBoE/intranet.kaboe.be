<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class NoteScreenPage extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_notescreen_page", \Database\Object\NoteScreenPage::class, orderField: 'id');
	}

	public function getBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}
}
