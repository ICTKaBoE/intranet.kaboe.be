<?php

namespace Controllers;

use Database\Repository\Setting;
use Database\Repository\UserProfile;
use O365\AuthenticationManager;
use Router\Helpers;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\User;

class DefaultController
{
	public $layout = "";

	const SHORT_TAG = ["meta", "link"];
	const COMPONENTS = [
		"footer" => \Controllers\COMPONENT\FooterComponentController::class,
		"header" => \Controllers\COMPONENT\HeaderComponentController::class,
		"schoolheader" => \Controllers\COMPONENT\SchoolHeaderComponentController::class,
		"modal" => \Controllers\COMPONENT\ModalComponentController::class,
		"navbar" => \Controllers\COMPONENT\NavbarComponentController::class,
		"pagetitle" => \Controllers\COMPONENT\PageTitleComponentController::class
	];

	public function index()
	{
		$this->write();
		return $this->getLayout();
	}

	public function write()
	{
		$this->createGlobalVariables();
		$this->storeLayout();
		$this->loadLoad();
		$this->loadComponents();
		$this->loadContent();
		$this->loadActions();
		$this->loadOthers();
		$this->loadSettings();
		$this->loadUserDetails();
	}

	protected function getLayout()
	{
		return $this->layout;
	}

	private function createGlobalVariables()
	{
		$this->pageId = Strings::underscoreToCamelCase(str_replace("/", "_", Helpers::getPageFolder()));
		$this->pageAction = "";

		if (Strings::equal(Helpers::getMethod(), "add")) $this->pageAction = "toevoegen";
		else if (Strings::equal(Helpers::getMethod(), "edit")) $this->pageAction = "bewerken";
		else if (Strings::equal(Helpers::getMethod(), "delete")) $this->pageAction = "verwijderen";

		$this->siteUrl = (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost();
	}

	private function storeLayout()
	{
		$overrides = json_decode(file_get_contents(LOCATION_BACKEND . "/config/layoutOverride.json"), true);

		$file = false;
		foreach ($overrides as $key => $paths) {
			if (Arrays::contains($paths, Helpers::getPageFolder())) {
				$file = $key;
				break;
			}
		}

		if (!$file) $file = "default";

		ob_start();
		require_once LOCATION_SHARED . "/layout/{$file}.php";
		$this->layout = ob_get_clean();
	}

	// Loaders

	private function loadLoad()
	{
		$this->layout = str_replace("{{load:head}}", $this->getLoad(), $this->layout);
		$this->layout = str_replace("{{load:body}}", $this->getLoad('body'), $this->layout);
	}

	private function loadComponents()
	{
		foreach (self::COMPONENTS as $component => $controller) {
			if (Strings::contains($this->layout, "{{component:{$component}}}")) $this->layout = str_replace("{{component:{$component}}}", (new $controller())->write(), $this->layout);
		}
	}

	private function loadContent()
	{
		$this->layout = str_replace("{{content:page:css}}", $this->getContentPageCss(), $this->layout);
		$this->layout = str_replace("{{content:page}}", $this->getContentPage(), $this->layout);
		$this->layout = str_replace("{{content:page:js}}", $this->getContentPageJs(), $this->layout);
	}

	private function loadActions()
	{
		$this->layout = str_replace("{{form:action}}", "{{api:url}}/form" . Helpers::getApiPath(), $this->layout);
		$this->layout = str_replace("{{calendar:action}}", "{{api:url}}/calendar" . Helpers::getApiPath(), $this->layout);
		$this->layout = str_replace("{{table:action}}", "{{api:url}}/table" . Helpers::getApiPath(), $this->layout);
		$this->layout = str_replace("{{select:action}}", "{{api:url}}/select" . Helpers::getApiPath(), $this->layout);
		$this->layout = str_replace("{{chart:action}}", "{{api:url}}/chart" . Helpers::getApiPath(), $this->layout);
		$this->layout = str_replace("{{notescreen:action}}", "{{api:url}}/notescreen" . Helpers::getApiPath(), $this->layout);
		$this->layout = str_replace("{{o365:connect}}", (string)AuthenticationManager::connect(autoRedirect: false), $this->layout);

		$this->layout = str_replace("{{api:url}}", "{{site:url}}/api/v1.0", $this->layout);
	}

	private function loadOthers()
	{
		$this->layout = str_replace("{{page:id}}", $this->pageId, $this->layout);
		$this->layout = str_replace("{{page:action}}", $this->pageAction, $this->layout);
		$this->layout = str_replace("{{site:url}}", $this->siteUrl, $this->layout);
	}

	private function loadSettings()
	{
		foreach ($this->getSettings() as $setting) {
			$this->layout = str_replace('{{' . $setting->id . '}}', $setting->value, $this->layout);
		}
	}

	private function loadUserDetails()
	{
		$user = User::getLoggedInUser();
		if (!$user) return;

		$profile = (new UserProfile)->getByUserId($user->id);

		foreach ($user as $key => $value) {
			$this->layout = str_replace("{{user:{$key}}}", $value, $this->layout);
		}

		foreach ($profile as $key => $value) {
			$this->layout = str_replace("{{user:profile:{$key}}}", $value, $this->layout);
		}
	}

	// Getters

	private function getLoad($position = 'head')
	{
		$json = json_decode(file_get_contents(LOCATION_BACKEND . "/config/load.json"), TRUE);
		$json = Arrays::getValue($json, $position, []);

		$html = "";

		foreach ($json as $line) {
			$isFolder = $line['isFolder'] ?? false;

			if ($isFolder) {
				die(var_dump(Path::normalize(LOCATION_PUBLIC . "/" . $line['folder'])));
				$files = array_values(array_diff(scandir(Path::normalize(LOCATION_PUBLIC . "/" . $line['folder'])), ['.', '..']));

				foreach ($files as $file) {
					$html .= "<{$line['tag']}";
					foreach ($line['attributes'] as $key => $value) {
						$value = str_replace("<PATH>", $line['path'] . "/" . $file, $value);
						$html .= " {$key}=\"{$value}\"";
					}

					$html .= ">" . ($this->isShortTag($line['tag']) ? "" : "</{$line['tag']}>");
					$html .= "\n";
				}
			} else {
				$html .= "<{$line['tag']}";

				foreach ($line['attributes'] as $key => $value) {
					$html .= " {$key}=\"{$value}\"";
				}

				$html .= ">" . ($this->isShortTag($line['tag']) ? "" : "</{$line['tag']}>");
				$html .= "\n";
			}
		}

		return $html;
	}

	private function getContentPage()
	{
		if (file_exists(LOCATION_FRONTEND . Helpers::getPageFolder() . "/index.php")) {
			ob_start();
			require_once LOCATION_FRONTEND . Helpers::getPageFolder() . "/index.php";
			return ob_get_clean();
		}

		return "";
	}

	private function getContentPageCss()
	{
		if (file_exists(LOCATION_FRONTEND . Helpers::getPageFolder() . "/style.css")) {
			return "<link rel=\"stylesheet\" href=\"{{site:url}}/frontend" . Helpers::getPageFolder() . "/style.css\" />";
		}

		return "";
	}

	private function getContentPageJs()
	{
		if (file_exists(LOCATION_FRONTEND . Helpers::getPageFolder() . "/functions.js")) {
			return "<script type=\"module\" src=\"{{site:url}}/frontend" . Helpers::getPageFolder() . "/functions.js\"></script>";
		}

		return "";
	}

	private function isShortTag($tag)
	{
		return Arrays::keyExists(self::SHORT_TAG, $tag);
	}

	private function getSettings()
	{
		return (new Setting)->get(order: false);
	}
}
