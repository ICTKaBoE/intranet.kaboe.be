<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Database\Repository\NoteScreenPage;

class NoteScreenArticle extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"schoolId",
		"notescreenPageId",
		"title",
		"content",
		"displayTime",
		"deleted"
	];

	protected $encodeAttributes = [
		"title"
	];

	public function link()
	{
		$this->page = (new NoteScreenPage)->get($this->notescreenPageId)[0];
	}
}
