<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class NoteScreenArticle extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_notescreen_article", \Database\Object\NoteScreenArticle::class, orderField: 'id');
	}

	public function getBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getBySchoolAndPage($schoolId, $pageId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('notescreenPageId', $pageId);

		return $this->executeSelect($statement);
	}
}
