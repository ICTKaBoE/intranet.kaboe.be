<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;

class InformatStudentSubscription extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_informat_student_subscription", \Database\Object\InformatStudentSubscription::class, orderField: 'start');
	}

	public function getByInformatUID($informatUID)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatUID', $informatUID);

		return Arrays::firstOrNull($this->executeSelect($statement));
	}

	public function getByInformatStudentUID($informatStudentUID)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatStudentUID', $informatStudentUID);

		return Arrays::orderBy($this->executeSelect($statement), "start");
	}

	public function getByInformatStudentUIDAndStatus($informatStudentUID, $status)
	{
		$statement = $this->prepareSelect();
		$statement->where('informatStudentUID', $informatStudentUID);
		$statement->where('status', $status);

		return Arrays::orderBy($this->executeSelect($statement), "start");
	}
}
