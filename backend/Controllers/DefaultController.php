<?php

namespace Controllers;

use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Database\Repository\Setting;
use Database\Repository\UserProfile;
use Middleware\DefaultMiddleware;
use O365\AuthenticationManager;
use Router\Helpers;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\User;

class DefaultController
{
	protected $layout = "";

	const SHORT_TAG = ["meta", "link"];
	const COMPONENTS = [
		"footer" => \Controllers\COMPONENT\FooterComponentController::class,
		"header" => \Controllers\COMPONENT\HeaderComponentController::class,
		"schoolheader" => \Controllers\COMPONENT\SchoolHeaderComponentController::class,
		"modal" => \Controllers\COMPONENT\ModalComponentController::class,
		"navbar" => \Controllers\COMPONENT\NavbarComponentController::class,
		"pagetitle" => \Controllers\COMPONENT\PageTitleComponentController::class,
		"floatingButtons" => \Controllers\COMPONENT\FloatingButtonsComponentController::class,
		"toast" => \Controllers\COMPONENT\ToastComponentController::class,
		"generalMessage" => \Controllers\COMPONENT\GeneralMessageComponentController::class
	];

	public function __construct()
	{
		DefaultMiddleware::handle();
	}

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
		$this->loadUrlParts();
		$this->loadUrlParams();
		$this->loadSettings();
		$this->loadModuleSettings();
		$this->loadUserDetails();
	}

	protected function getLayout()
	{
		return preg_replace("/{{.*?}}/", "", $this->layout);
	}

	private function createGlobalVariables()
	{
		$this->pageId = Strings::underscoreToCamelCase(str_replace("/", "_", Helpers::getReletiveUrl()));
		$this->pageAction = "";

		$this->siteUrl = (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost();
	}

	private function storeLayout()
	{
		$overrides = json_decode(file_get_contents(LOCATION_BACKEND . "/config/layoutOverride.json"), true);
		$url = rtrim(Helpers::request()->getLoadedRoute()->getUrl(), "/");
		$route = Helpers::getReletiveUrl();

		$file = false;
		foreach ($overrides as $key => $paths) {
			if (Arrays::contains($paths, $route) || Arrays::contains($paths, $url)) {
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
		$this->layout = str_replace("{{form:url:short}}", "{{api:url}}/form", $this->layout);
		$this->layout = str_replace("{{calendar:url:short}}", "{{api:url}}/calendar", $this->layout);
		$this->layout = str_replace("{{table:url:short}}", "{{api:url}}/table", $this->layout);
		$this->layout = str_replace("{{select:url:short}}", "{{api:url}}/select", $this->layout);
		$this->layout = str_replace("{{chart:url:short}}", "{{api:url}}/chart", $this->layout);
		$this->layout = str_replace("{{notescreen:url:short}}", "{{api:url}}/notescreen", $this->layout);
		$this->layout = str_replace("{{taskboard:url:short}}", "{{api:url}}/taskboard", $this->layout);

		$this->layout = str_replace("{{form:url:full}}", "{{api:url}}/form/{{url:part:module}}/{{url:part:page}}", $this->layout);
		$this->layout = str_replace("{{calendar:url:full}}", "{{api:url}}/calendar/{{url:part:module}}/{{url:part:page}}", $this->layout);
		$this->layout = str_replace("{{table:url:full}}", "{{api:url}}/table/{{url:part:module}}/{{url:part:page}}", $this->layout);
		$this->layout = str_replace("{{select:url:full}}", "{{api:url}}/select/{{url:part:module}}/{{url:part:page}}", $this->layout);
		$this->layout = str_replace("{{chart:url:full}}", "{{api:url}}/chart/{{url:part:module}}/{{url:part:page}}", $this->layout);
		$this->layout = str_replace("{{notescreen:url:full}}", "{{api:url}}/notescreen/{{url:part:module}}/{{url:part:page}}", $this->layout);
		$this->layout = str_replace("{{taskboard:url:full}}", "{{api:url}}/taskboard/{{url:part:module}}/{{url:part:page}}", $this->layout);

		$this->layout = str_replace("{{o365:connect}}", (string)AuthenticationManager::connect(autoRedirect: false), $this->layout);

		$this->layout = str_replace("{{api:url}}", "{{site:url}}/api/v{{api.version}}", $this->layout);
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

	private function loadModuleSettings()
	{
		foreach ($this->getModuleSettings() as $setting) {
			$this->layout = str_replace('{{module:' . $setting->key . '}}', $setting->value, $this->layout);
		}
	}

	private function loadUrlParts()
	{
		$this->layout = str_replace("{{url:part:module}}", Helpers::getModule(), $this->layout);
		$this->layout = str_replace("{{url:part:page}}", Helpers::getPage(), $this->layout);
	}

	private function loadUrlParams()
	{
		foreach (Helpers::url()->getParams() as $key => $value) {
			$this->layout = str_replace("{{url:param:{$key}}}", $value, $this->layout);
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
		$fileTags = ["src", "href"];

		foreach ($json as $line) {
			$isFolder = $line['isFolder'] ?? false;

			if ($isFolder) {
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
					if (Arrays::contains($fileTags, $key)) {
						$value .= "?" . filemtime(str_replace("{{site:url}}", LOCATION_ROOT, $value));
					}

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
		$content = "";

		if (file_exists(LOCATION_FRONTEND . Helpers::getReletiveUrl() . "/index.php")) {
			ob_start();
			require_once LOCATION_FRONTEND . Helpers::getReletiveUrl() . "/index.php";
			$content = ob_get_clean();
		}

		if (file_exists(LOCATION_FRONTEND . Helpers::getReletiveUrl() . "/modal.php")) {
			ob_start();
			require_once LOCATION_FRONTEND . Helpers::getReletiveUrl() . "/modal.php";
			$content .= ob_get_clean();
		}

		return $content;
	}

	private function getContentPageCss()
	{
		if (file_exists(LOCATION_FRONTEND . Helpers::getReletiveUrl() . "/style.css")) {
			return "<link rel=\"stylesheet\" href=\"{{site:url}}/frontend" . Helpers::getReletiveUrl() . "/style.css\" />";
		}

		return "";
	}

	private function getContentPageJs()
	{
		if (file_exists(LOCATION_FRONTEND . Helpers::getReletiveUrl() . "/functions.js")) {
			return "<script type=\"module\" src=\"{{site:url}}/frontend" . Helpers::getReletiveUrl() . "/functions.js\"></script>";
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

	private function getModuleSettings()
	{
		return (new ModuleSetting)->getByModule((new Module)->getByModule(Helpers::getModule())->id);
	}
}
