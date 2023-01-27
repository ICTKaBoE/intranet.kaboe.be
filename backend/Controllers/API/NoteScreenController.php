<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Controllers\ApiController;
use Database\Object\NoteScreenArticle as ObjectNoteScreenArticle;
use Database\Object\NoteScreenPage as ObjectNoteScreenPage;
use Database\Repository\NoteScreenPage;
use Database\Repository\NoteScreenArticle;
use Database\Repository\School;
use Database\Repository\UserProfile;
use Security\User;

class NoteScreenController extends ApiController
{
	public function viewscreen($prefix, $schoolId)
	{
		$this->appendToJson("settings", (new School)->get($schoolId)[0]);
		$this->appendToJson("pages", array_values((new NoteScreenPage)->getBySchoolId($schoolId)));
		$this->appendToJson("articles", array_values((new NoteScreenArticle)->getBySchoolId($schoolId)));
		$this->handle();
	}

	public function pages($prefix, $method, $id = null)
	{
		$name = Helpers::input()->post('name')->getValue();

		if (!Input::check($name) && Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$repo = new NoteScreenPage;
			$page = $method == 'edit' ? $repo->get($id)[0] : new ObjectNoteScreenPage;

			$page->schoolId = (new UserProfile)->getByUserId(User::getLoggedInUser()->id)->mainSchoolId;
			$page->name = $name;
			$repo->set($page);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/notescreen/pages");
		$this->handle();
	}

	public function deletePages($prefix, $id)
	{
		$ids = explode("-", $id);

		$repo = new NoteScreenPage;

		foreach ($ids as $id) {
			$object = $repo->get($id)[0];
			$object->deleted = 1;

			$repo->set($object);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/notescreen/pages");
		$this->handle();
	}

	public function articles($prefix, $method, $id = null)
	{
		$title = Helpers::input()->post('title')->getValue();
		$pageId = Helpers::input()->post('notescreenPageId')->getValue();
		$content = Helpers::input()->post('content')->getValue();

		if (!Input::check($title) && Input::empty($title)) $this->setValidation("title", "Titel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
		if (!Input::check($pageId, Input::INPUT_TYPE_INT) && Input::empty($pageId)) $this->setValidation("pageId", "Pagina moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

		if ($this->validationIsAllGood()) {
			$repo = new NoteScreenArticle;
			$article = $method == 'edit' ? $repo->get($id)[0] : new ObjectNoteScreenArticle;

			$article->schoolId = (new UserProfile)->getByUserId(User::getLoggedInUser()->id)->mainSchoolId;
			$article->notescreenPageId = $pageId;
			$article->title = $title;
			$article->content = $content;
			$repo->set($article);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/notescreen/articles");
		$this->handle();
	}

	public function deleteArticles($prefix, $id)
	{
		$ids = explode("-", $id);

		$repo = new NoteScreenArticle;

		foreach ($ids as $id) {
			$object = $repo->get($id)[0];
			$object->deleted = 1;

			$repo->set($object);
		}

		if (!$this->validationIsAllGood()) {
			$this->setHttpCode(400);
		} else $this->appendToJson('redirect', "/{$prefix}/notescreen/articles");
		$this->handle();
	}
}
