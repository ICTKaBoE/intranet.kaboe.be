<?php

namespace Helpers;

use PhpZip\ZipFile;

class ZIP extends ZipFile
{
	public function __construct($location)
	{
		$this->location = $location;
		parent::__construct();
	}

	public function save()
	{
		$this->saveAsFile($this->location)->close();
	}
}
