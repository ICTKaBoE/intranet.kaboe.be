<?php

namespace Database\Repository;

use Database\Interface\Repository;
use Router\Helpers;
use Security\User;

class Log extends Repository
{
	const TYPE_INFO = "I";
	const TYPE_WARNING = "W";
	const TYPE_ERROR = "E";

	public function __construct()
	{
		parent::__construct("tbl_log", \Database\Object\Log::class, orderField: 'creationDateTime', orderDirection: 'DESC', deletedField: false);
	}

	static public function write($type = self::TYPE_INFO, $date = null, $userId = null, $description = null)
	{
		$repo = new self;
		$log = new $repo->object;

		$log->type = $type;
		$log->date = $date;
		$log->userId = ($userId == null ? User::getLoggedInUser()->id : $userId);
		$log->route = Helpers::request()->getReferer() ?? Helpers::url()->getAbsoluteUrl();
		$log->description = $description;

		$repo->set($log);
	}
}
