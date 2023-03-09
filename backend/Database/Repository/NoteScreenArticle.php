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
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->schoolId, $schoolId));
	}

	public function getBySchoolAndPage($schoolId, $pageId)
	{
		$items = $this->get();
		return Arrays::filter($items, fn ($i) => Strings::equal($i->schoolId, $schoolId) && Strings::equal($i->notescreenPageId, $pageId));
	}
}
