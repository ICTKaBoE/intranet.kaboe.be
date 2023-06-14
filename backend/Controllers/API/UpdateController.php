<?php

namespace Controllers\API;

use Database\Database;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;

class UpdateController extends ApiController
{
	public function database($vMajor, $vMinor)
	{
		$version = "v{$vMajor}.{$vMinor}";
		$file = LOCATION_SQL . "/{$version}.sql";

		$content = file_get_contents($file);
		$content = str_replace(["\r", "\n"], "", $content);
		$lines = explode(";", $content);

		$lines = Arrays::filterNotBlank($lines);
		$lines = Arrays::filter($lines, fn ($l) => !Strings::startsWith($l, "-- "));
		$lines = Arrays::map($lines, fn ($l) => trim($l));

		try {
			$database = Database::getInstance();
			$connection = $database->getConnection();
			$database->beginTransaction();

			foreach ($lines as $line) {
				$stmt = $connection->prepare($line);
				$stmt->execute();
			}

			$database->commit();
			$this->appendToJson("update", "Success");
		} catch (\Exception $e) {
			$database->rollback();
			$this->appendToJson("update", "Failed: {$e->getMessage()}");
		}

		$this->handle();
	}
}
