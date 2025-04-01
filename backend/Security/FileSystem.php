<?php

namespace Security;

use DirectoryIterator;
use Ouzo\Utilities\Clock;
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
		// $path = "https://kaboe.be" . $path;

		return $path;
	}

	static public function WriteFile($path, $content)
	{
		$path = Path::normalize($path);
		$stream = fopen($path, "wb");
		$result = fwrite($stream, $content);
		fclose($stream);

		return ($result !== 0);
	}

	static public function RemoveFile($path)
	{
		return unlink($path);
	}

	static public function unifyPath($path)
	{
		$path = str_replace(LOCATION_ROOT, "", $path);
		return str_replace("\\", "/", $path);
	}

	static public function getFiles($path)
	{
		if (!self::PathExists($path)) return false;
		return array_values(array_diff(scandir(Path::normalize($path)), [".", ".."]));
	}

	static public function getLatestFile($path)
	{
		if (!self::PathExists($path)) return false;

		$filePath = null;
		$lastClock = Clock::at("1970-01-01");

		$files = self::getFiles($path);

		foreach ($files as $file) {
			$filename = pathinfo($file, PATHINFO_FILENAME);
			$lastEdit = date("Y-m-d H:i:s", $filename);

			if (Clock::at($lastEdit)->isAfter($lastClock)) {
				$filePath = $file;
				$lastClock = Clock::at($lastEdit);
			}
		}

		return rtrim($path, "/") . "/{$filePath}";
	}
}
