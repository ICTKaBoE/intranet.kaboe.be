<?php

namespace Controllers;

use Database\Repository\Module;
use Database\Repository\ModuleSetting;
use Ouzo\Utilities\Path;
use Ouzo\Utilities\Arrays;
use Database\Repository\Setting;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\User;

class Controller
{
	const SHORT_TAG = ["meta", "link"];

	function index()
	{
		$this->write();
		return $this->layout;
	}

	function write()
	{
		$this->getLayout();
		$this->loadLoad();
		$this->loadComponents();
		$this->loadContent();
		$this->loadOthers();
		$this->loadSettings();
		$this->loadModuleSettings();
		$this->loadUserDetails();
	}

	private function getLayout()
	{
		$default = User::isSignedIn();
		$file = ($default ? "default" : "nocomponents");

		ob_start();
		require_once LOCATION_PUBLIC . "/Layout/{$file}.php";
		$this->layout = ob_get_clean();
	}

	private function getSettings()
	{
		return (new Setting)->get(order: false);
	}

	private function getModuleSettings()
	{
		$module = (new Module)->getByModule(Helpers::get_module());
		return (new ModuleSetting)->getByModule($module->id);
	}

	private function getContentPage()
	{
		$url = rtrim(Helpers::url()->getRelativeUrl(), "/");

		ob_start();
		require_once LOCATION_PUBLIC . "/Pages{$url}/index.php";
		return ob_get_clean();
	}

	private function getContentPageCss()
	{
		$url = rtrim(Helpers::url()->getRelativeUrl(), "/");

		if (file_exists(LOCATION_PUBLIC . "/Pages{$url}/main.css")) {
			return "<link rel=\"stylesheet\" href=\"{{site:url}}/public/Pages{$url}/main.css\" />";
		}

		return "";
	}

	private function getContentPageJs()
	{
		$url = rtrim(Helpers::url()->getRelativeUrl(), "/");

		if (file_exists(LOCATION_PUBLIC . "/Pages{$url}/main.js")) {
			return "<script type=\"module\" src=\"{{site:url}}/public/Pages{$url}/main.js\"></script>";
		}

		return "";
	}

	private function loadSettings()
	{
		$settings = $this->getSettings();

		foreach ($settings as $setting) {
			$this->layout = str_replace('{{' . $setting->id . '}}', $setting->value, $this->layout);
		}
	}

	private function loadModuleSettings()
	{
		$settings = $this->getModuleSettings();

		foreach ($settings as $setting) {
			$this->layout = str_replace('{{' . $setting->key . '}}', $setting->value, $this->layout);
		}
	}

	private function loadUserDetails()
	{
		$user = User::getLoggedInUser();

		foreach ($user as $key => $value) $this->layout = str_replace("{{user:" . $key . "}}", $value, $this->layout);
	}

	private function loadLoad()
	{
		$this->layout = str_replace("{{load:head}}", $this->getLoad(), $this->layout);
		$this->layout = str_replace("{{load:body}}", $this->getLoad('body'), $this->layout);
	}

	private function loadContent()
	{
		$this->layout = str_replace("{{content:page:css}}", $this->getContentPageCss(), $this->layout);
		$this->layout = str_replace("{{content:page}}", $this->getContentPage(), $this->layout);
		$this->layout = str_replace("{{content:page:js}}", $this->getContentPageJs(), $this->layout);
	}

	private function loadComponents()
	{
		if (Strings::contains($this->layout, "{{component:header}}")) $this->layout = str_replace("{{component:header}}", (new ComponentController('header'))->write(), $this->layout);
		if (Strings::contains($this->layout, "{{component:navbar}}")) $this->layout = str_replace("{{component:navbar}}", (new ComponentController('navbar'))->write(), $this->layout);
		if (Strings::contains($this->layout, "{{component:pagetitle}}")) $this->layout = str_replace("{{component:pagetitle}}", (new ComponentController('pagetitle'))->write(), $this->layout);
		if (Strings::contains($this->layout, "{{component:footer}}")) $this->layout = str_replace("{{component:footer}}", (new ComponentController('footer'))->write(), $this->layout);
		if (Strings::contains($this->layout, "{{component:modal}}")) $this->layout = str_replace("{{component:modal}}", (new ComponentController('modal'))->write(), $this->layout);
	}

	private function loadOthers()
	{
		$url = ltrim(rtrim(Helpers::url()->getRelativeUrl(), "/"), "/");
		$id = Strings::underscoreToCamelCase(str_replace("/", "_", $url));
		$formActionGet = "{{site:url}}/app/scripts/GET/{{script:filename}}";
		$formActionPost = "{{site:url}}/app/scripts/POST/{{script:filename}}";
		$formActionExport = "{{site:url}}/app/scripts/EXPORT/{{script:filename}}";

		$calendarActionGet = "{{site:url}}/app/scripts/GET/Calendar/{{script:filename}}";
		$tableActionGet = "{{site:url}}/app/scripts/GET/Table/{{script:filename}}";

		$this->layout = str_replace("{{id}}", $id, $this->layout);
		$this->layout = str_replace("{{form:action:get}}", $formActionGet, $this->layout);
		$this->layout = str_replace("{{form:action:post}}", $formActionPost, $this->layout);
		$this->layout = str_replace("{{form:action:export}}", $formActionExport, $this->layout);
		$this->layout = str_replace("{{calendar:action:get}}", $calendarActionGet, $this->layout);
		$this->layout = str_replace("{{table:action:get}}", $tableActionGet, $this->layout);
		$this->layout = str_replace("{{script:filename}}", lcfirst($id) . ".php", $this->layout);
		$this->layout = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $this->layout);
	}

	private function getLoad($position = 'head')
	{
		$json = json_decode(file_get_contents(LOCATION_APP . "/config/load.json"), TRUE);
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

	private function isShortTag($tag)
	{
		return Arrays::keyExists(self::SHORT_TAG, $tag);
	}
}
