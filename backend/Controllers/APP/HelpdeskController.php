<?php

namespace Controllers\APP;

use Router\Helpers;
use Controllers\DefaultController;
use Database\Repository\HelpdeskAction;
use Database\Repository\HelpdeskThread;

class HelpdeskController extends DefaultController
{
	const TEMPLATE_THREAD = '<div class="card mb-3">
								<div class="card-header card-header-light">
									<h3 class="card-title">
										{{thread:creator:fullName}}
										<span class="card-subtitle">{{thread:datetime}}</span>
									</h3>
								</div>

								<div class="card-body">{{thread:content}}</div>
							</div>{{helpdesk:thread}}';

	const TEMPLATE_ACTIONS = 	'<div class="list-group-item">
									<div class="row align-items-center">
										<div class="col">
											<div class="text-reset d-block">{{action:description}}</div>
											<div class="d-block text-muted mt-n1">{{action:datetime}}</div>
										</div>
									</div>
								</div>{{helpdesk:actions}}';

	public function details()
	{
		$this->write();
		$this->writeThread();
		$this->writeActions();
		$this->cleanUp();
		return $this->getLayout();
	}

	private function writeThread()
	{
		$id = Helpers::url()->getParam("id");
		$threads = (new HelpdeskThread)->getByHelpdeskId($id);

		foreach ($threads as $thread) {
			$template = self::TEMPLATE_THREAD;

			foreach ($thread as $key => $value) $template = str_replace("{{thread:{$key}}}", $value, $template);

			$thread->link();
			$template = str_replace("{{thread:creator:fullName}}", $thread->creator->fullName, $template);
			$this->layout = str_replace("{{helpdesk:thread}}", $template, $this->layout);
		}
	}

	private function writeActions()
	{
		$id = Helpers::url()->getParam("id");
		$actions = (new HelpdeskAction)->getByHelpdeskId($id);

		foreach ($actions as $action) {
			$template = self::TEMPLATE_ACTIONS;

			foreach ($action as $key => $value) $template = str_replace("{{action:{$key}}}", $value, $template);

			$this->layout = str_replace("{{helpdesk:actions}}", $template, $this->layout);
		}
	}

	private function cleanUp()
	{
		$this->layout = str_replace("{{helpdesk:thread}}", "", $this->layout);
		$this->layout = str_replace("{{helpdesk:details}}", "", $this->layout);
		$this->layout = str_replace("{{helpdesk:actions}}", "", $this->layout);
	}
}
