<?php

namespace Database\Repository;

use Database\Interface\Repository;

class Library extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_library", \Database\Object\Library::class, orderField: 'title');
	}

	public function getBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);

		return $this->executeSelect($statement);
	}

	public function getByBookId($bookId)
	{
		$statement = $this->prepareSelect();
		$statement->where('id', $bookId);

		return $this->executeSelect($statement);
	}

	public function getByCategory($category)
	{
		$statement = $this->prepareSelect();
		$statement->where('category', $category);

		return $this->executeSelect($statement);
	}

	public function getBySchoolIdAndCategory($schoolId, $category)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('category', $category);

		return $this->executeSelect($statement);
	}

	public function getByLend()
	{
		$statement = $this->prepareSelect();
		$statement->where('numberOfAvailableCopies', '<', new \ClanCats\Hydrahon\Query\Expression('numberOfCopies'));
		
		return $this->executeSelect($statement);
	}

	public function getByLendBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('numberOfAvailableCopies', '<', new \ClanCats\Hydrahon\Query\Expression('numberOfCopies'));
		
		return $this->executeSelect($statement);
	}

	public function getByAvailableBySchoolId($schoolId)
	{
		$statement = $this->prepareSelect();
		$statement->where('schoolId', $schoolId);
		$statement->where('numberOfAvailableCopies', '>', 0);

		return $this->executeSelect($statement);
	}

	public function checkAlreadyExist($schoolId, $author, $title, $isdn, $category, $id = null)
	{
		$statement = $this->prepareSelect();
		$statement->where("schoolId", $schoolId);
		$statement->where("author", $author);
		$statement->where("title", $title);
		$statement->where("isdn", $isdn);
		$statement->where("category", $category);
		if (!is_null($id)) $statement->where('id', '!=', $id);

		return $this->executeSelect($statement);
	}
}
