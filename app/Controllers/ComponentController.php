<?php

namespace Controllers;

use Ouzo\Utilities\Arrays;
use Router\Helpers;

class ComponentController
{
	public function __construct($component)
	{
		$this->component = $component;
		$this->url = rtrim(Helpers::url()->getRelativeUrl(), "/");
		$this->showNotOn = [
			'header' => [
				'/login',
				'/forgot_password',
				'/error/404'
			],
			'navbar' => [
				'/login',
				'/forgot_password',
				'/error/404',
				'/selectModule'
			],
			'pagetitle' => [
				'/login',
				'/forgot_password',
				'/error/404',
				'/selectModule'
			],
			'footer' => [],
			'modal' => []
		];
	}

	function write(): string
	{
		if (!Arrays::contains($this->showNotOn[$this->component], $this->url)) {
			ob_start();
			require_once LOCATION_PUBLIC . "/Components/{$this->component}.php";
			$layout = ob_get_clean();

			return $layout;
		}

		return "";
	}
}
