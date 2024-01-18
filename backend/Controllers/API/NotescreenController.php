<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Arrays;
use Database\Repository\log;
use Controllers\ApiController;
use Database\Object\NoteScreenArticle as ObjectNoteScreenArticle;
use Database\Repository\School;
use Database\Repository\NoteScreenPage;
use Database\Repository\NoteScreenArticle;
use Database\Object\NoteScreenPage as ObjectNoteScreenPage;

class NotescreenController extends ApiController
{
    // GET
    public function getPages($view, $id = null)
    {
        $schoolId = Helpers::url()->getParam("schoolId");
        $pages = (is_null($id) ? (new NoteScreenPage)->getBySchoolId($schoolId) : Arrays::firstOrNull((new NoteScreenPage)->get($id)));

        if ($view == "table") {
            $this->appendToJson(
                'columns',
                [
                    [
                        "type" => "checkbox",
                        "class" => ["w-1"],
                        "data" => "id"
                    ],
                    [
                        "title" => "Titel",
                        "data" => "name",
                    ],
                ]
            );
            $this->appendToJson(['rows'], $pages);
        } else if ($view == "form") $this->appendToJson(['fields'], $pages);
        else if ($view == "select") $this->appendToJson(['items'], $pages);

        $this->handle();
    }

    public function getArticles($view, $id = null)
    {
        if (
            $view == "table"
        ) {
            $schoolId = Helpers::url()->getParam("schoolId");

            $rows = (new NoteScreenArticle)->getBySchoolId($schoolId);
            Arrays::each($rows, fn ($r) => $r->link());
            $this->appendToJson(
                'columns',
                [
                    [
                        "type" => "checkbox",
                        "class" => ["w-1"],
                        "data" => "id"
                    ],
                    [
                        "title" => "Pagina",
                        "data" => "page.name",
                        "width" => 200
                    ],
                    [
                        "title" => "Titel",
                        "data" => "title",
                    ],
                ]
            );
            $this->appendToJson(['rows'], $rows);
        } else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new NoteScreenArticle)->get($id)));

        $this->handle();
    }

    public function getViewScreen($view)
    {
        $schoolId = Helpers::url()->getParam("schoolId");
        $this->appendToJson("settings", (new School)->get($schoolId)[0]);
        $this->appendToJson("pages", array_values((new NoteScreenPage)->getBySchoolId($schoolId)));
        $this->appendToJson("articles", array_values((new NoteScreenArticle)->getBySchoolId($schoolId)));
        $this->handle();
    }

    // POST
    public function postPages($view, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $faction = Helpers::input()->post('faction', false)->getValue();

        $notescreenPageRepo = new NoteScreenPage;

        if ($faction !== "delete") {
            if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", "Naam moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

            if ($this->validationIsAllGood()) {
                $page = is_null($id) ? new ObjectNoteScreenPage() : $notescreenPageRepo->get($id)[0];

                $page->schoolId = $schoolId;
                $page->name = $name;
                $notescreenPageRepo->set($page);
            }
        } else {
            $ids = Helpers::input()->post('ids')->getValue();
            $ids = explode("-", $ids);

            foreach ($ids as $_id) {
                $notescreenPageObject = Arrays::firstOrNull($notescreenPageRepo->get($_id));

                if (!is_null($notescreenPageObject)) {
                    $notescreenPageObject->deleted = 1;
                    $notescreenPageRepo->set($notescreenPageObject);
                }
            }
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
            if ($faction !== "delete") $this->setToast("Meldingenscherm - Pagina's", "De pagina is opgeslagen!");
            else $this->setToast("Meldingenscherm - Pagina's", "De pagina's is/zijn verwijderd!");
        }

        $this->handle();
    }

    public function postArticles($view, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $title = Helpers::input()->post('title')->getValue();
        $notescreenPageId = Helpers::input()->post('notescreenPageId')->getValue();
        $content = Helpers::input()->post('content')->getValue();
        $displayTime = Helpers::input()->post('displayTime')->getValue();
        $faction = Helpers::input()->post('faction', false)->getValue();

        $notescreenArticleRepo = new NoteScreenArticle;

        if ($faction !== "delete") {
            if (!Input::check($title) || Input::empty($title)) $this->setValidation("title", "Titel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            if (!Input::check($notescreenPageId) || Input::empty($notescreenPageId)) $this->setValidation("notescreenPageId", "Pagina moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            if (!Input::check($content) || Input::empty($content)) $this->setValidation("content", "Inhoud moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            if (!Input::check($displayTime) || Input::empty($displayTime)) $this->setValidation("displayTime", "Toon tijd moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);

            if ($this->validationIsAllGood()) {
                $notescreenArticleObject = is_null($id) ? new ObjectNoteScreenArticle : Arrays::firstOrNull($notescreenArticleRepo->get($id));

                $notescreenArticleObject->schoolId = $schoolId;
                $notescreenArticleObject->title = $title;
                $notescreenArticleObject->notescreenPageId = $notescreenPageId;
                $notescreenArticleObject->content = $content;
                $notescreenArticleObject->displayTime = $displayTime;

                $notescreenArticleRepo->set($notescreenArticleObject);
            }
        } else {
            $ids = Helpers::input()->post('ids')->getValue();
            $ids = explode("-", $ids);

            foreach ($ids as $_id) {
                $notescreenArticleObject = Arrays::firstOrNull($notescreenArticleRepo->get($_id));

                if (!is_null($notescreenArticleObject)) {
                    $notescreenArticleObject->deleted = 1;
                    $notescreenArticleRepo->set($notescreenArticleObject);
                }
            }
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
            if ($faction !== "delete") $this->setToast("Meldingenscherm - Artikelen", "Het artikel is opgeslagen!");
            else $this->setToast("Meldingenscherm - Artikelen", "De artikelen is/zijn verwijderd!");
        }

        $this->handle();
    }
}
