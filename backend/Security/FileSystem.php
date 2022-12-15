<?php

namespace Security;

use Ouzo\Utilities\Path;
use Router\Helpers;

abstract class FileSystem
{
	static public function CreateFolder($path)
	{
		$path = Path::normalize($path);
		if (!self::PathExists($path)) mkdir($path, 0777, true);

		return $path;
	}

	static public function PathExists($path)
	{
		$path = Path::normalize($path);
		return file_exists($path);
	}

	static public function GetDownloadLink($path)
	{
		$path = Path::normalize($path);
		$path = self::unifyPath($path);
		$path = (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost() . $path;

		return $path;
	}

	static public function unifyPath($path)
	{
		$path = str_replace(LOCATION_ROOT, "", $path);
		return str_replace("\\", "/", $path);
	}
}
