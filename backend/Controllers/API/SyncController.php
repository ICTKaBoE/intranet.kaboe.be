<?php

namespace Controllers\API;

use Router\Helpers;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Module;
use Database\Repository\SyncStudent;
use Database\Repository\ModuleSetting;

class SyncController extends ApiController
{
	// GET
	public function getStudents()
	{
		$items = (new SyncStudent)->get();
		$items = Arrays::filter($items, fn ($i) => !Strings::equal($i->action, "N"));
		$this->appendToJson("items", array_values($items));

		$this->handle();
	}

	// POST
	public function syncStudentUpdate($id)
	{
		$lastAdSyncSuccessAction = Helpers::input()->post('lastAdSyncSuccessAction')?->getValue();
		$lastAdSyncTime = Helpers::input()->post('lastAdSyncTime')?->getValue();
		$lastAdSyncError = Helpers::input()->post('lastAdSyncError')?->getValue();
		$action = Helpers::input()->post('action')?->getValue();

		$syncStudentRepo = new SyncStudent;
		$item = $syncStudentRepo->get($id)[0];
		$item->lastAdSyncSuccessAction = (Strings::isNotBlank($lastAdSyncSuccessAction) ? $lastAdSyncSuccessAction : NULL);
		$item->lastAdSyncTime = (Strings::isNotBlank($lastAdSyncTime) ? $lastAdSyncTime : $item->lastAdSyncTime);
		$item->lastAdSyncError = (Strings::isNotBlank($lastAdSyncError) ? $lastAdSyncError : NULL);
		$item->action = (Strings::isNotBlank($action) ? $action : $item->action);
		$syncStudentRepo->set($item);
	}

	public function syncStudentTimeUpdate()
	{
		$module = (new Module)->getByModule('synchronisation');
		$moduleSettingRepo = new ModuleSetting;

		$studentLastSyncTime = $moduleSettingRepo->getByModuleAndKey($module->id, "studentLastSyncTime");
		$studentLastSyncTime->value = Clock::nowAsString("Y-m-d H:i:s");
		$moduleSettingRepo->set($studentLastSyncTime);

		$this->handle();
	}
}
