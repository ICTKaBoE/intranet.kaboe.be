<?php

namespace Database\Repository;

use Ouzo\Utilities\Clock;
use Database\Interface\Repository;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Mail extends Repository
{
	public function __construct()
	{
		parent::__construct("tbl_mail", \Database\Object\Mail::class, orderField: 'sendAt', orderDirection: 'DESC');
	}

	public function getNeedToSendNow()
	{
		$statement = $this->prepareSelect();
		$statement->where("sent", 0);
		$items = $this->executeSelect($statement);
		$items = Arrays::filter($items, fn ($i) => Strings::equal(Clock::nowAsString("Y-m-d H:i:00"), $i->sendAt));

		return $items;
	}

	static public function write($subject, $body, $receivers, $sendAt = "now", $replyTo = [], $html = true)
	{
		$repo = new self;
		$obj = new $repo->object;

		$obj->subject = $subject;
		$obj->body = str_replace([PHP_EOL, "\t"], "", $body);
		$obj->html = $html;
		$obj->receivers = json_encode($receivers);
		$obj->replyTo = json_encode($replyTo);
		$obj->sendAt = (Strings::equal($sendAt, "now") ? Clock::now()->plusMinutes(1) : Clock::at($sendAt))->format("Y-m-d H:i:00");

		$repo->set($obj);
	}
}
