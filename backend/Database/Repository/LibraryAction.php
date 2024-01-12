<?php

namespace Database\Repository;

use Database\Interface\Repository;

class LibraryAction extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_library_action", \Database\Object\LibraryAction::class, orderField: 'creationDateTime', orderDirection: 'DESC');
	}

	public function getByBookId($bookId)
	{
		$statement = $this->prepareSelect();
		$statement->where('bookId', $bookId);

		return $this->executeSelect($statement);
	}
}