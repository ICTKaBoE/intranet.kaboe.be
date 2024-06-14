<?php

namespace Controllers\API;

use Router\Helpers;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Controllers\ApiController;
use Database\Object\Library as ObjectLibrary;
use Database\Object\LibraryAction as ObjectLibraryAction;
use Database\Repository\InformatStaff;
use Database\Repository\InformatStudent;
use Database\Repository\Library;
use Database\Repository\LibraryAction;
use Database\Repository\Log;
use Database\Repository\School;
use Security\Input;
use Security\User;

class LibraryController extends ApiController
{
    const TEMPLATE_ACTIONS =     '<div class="list-group-item">
									<div class="row align-items-center">
										<div class="col">
											<div class="text-reset d-block">{{action:description}}</div>
											<div class="d-block text-muted mt-n1">{{action:datetime}}</div>
										</div>
									</div>
								</div>';

    // GET
    public function getDashboard($view, $id = null)
    {
        if ($view == "chart") {
            $schoolRepo = new School;
            $libraryRepo = new Library;

            $this->appendToJson(["xaxis", "categories"], Arrays::map($schoolRepo->get(), fn ($s) => $s->name));
            $series = [
                [
                    "name" => "Boeken"
                ],
                [
                    "name" => "Beschikbare boeken"
                ],
                [
                    "name" => "Uitgeleende boeken"
                ]
            ];

            foreach ($schoolRepo->get() as $idx => $school) {
                $series[0]["data"][$idx] = count($libraryRepo->getBySchoolId($school->id));
                $series[1]["data"][$idx] = count($libraryRepo->getByAvailableBySchoolId($school->id));
                $series[2]["data"][$idx] = count($libraryRepo->getByLendBySchoolId($school->id));
            }
            $this->appendToJson("series", $series);
        }
        $this->handle();
    }

    public function getLibrary($view, $id = null)
    {
        if ($view == "select") {
            $schoolId = Helpers::input()->get("parentValue");

            $this->appendToJson("items", (new Library)->getBySchoolId($schoolId));
        } else if ($view == "table") {
            $schoolId = Helpers::url()->getParam("schoolId");
            $category = Helpers::url()->getParam("category");

            $this->appendToJson(
                'columns',
                [
                    [
                        "type" => "checkbox",
                        "class" => ["w-1"],
                        "data" => "id"
                    ],
                    [
                        "type" => "icon",
                        "title" => "Beschikbaar",
                        "data" => "available",
                        "width" => 100
                    ],
                    [
                        "type" => "badge",
                        "title" => "School",
                        "data" => "school.name",
                        "backgroundColorCustom" => "school.color",
                        "width" => 120
                    ],
                    [
                        "title" => "Categorie",
                        "data" => "categoryFull",
                        "width" => 120
                    ],
                    [
                        "title" => "Auteur",
                        "data" => "author",
                        "width" => 150
                    ],
                    [
                        "title" => "Titel",
                        "data" => "title",
                        "width" => 200
                    ],
                    [
                        "title" => "ISDN",
                        "data" => "isdn",
                        "width" => 120
                    ],
                    [
                        "title" => "Aantal exemplaren",
                        "data" => "numberOfCopies",
                        "width" => 100
                    ],
                    [
                        "title" => "Aantal beschikbare exemplaren",
                        "data" => "numberOfAvailableCopies",
                        "width" => 100
                    ],
                    [
                        "title" => "Laatste activiteit",
                        "data" => "lastAction",
                        "width" => 200,
                    ]
                ]
            );

            $rows = (is_null($id) ? (is_null($category) ? (new Library)->getBySchoolId($schoolId) : (new Library)->getBySchoolIdAndCategory($schoolId, $category)) : Arrays::firstOrNull((new Library)->get($id)));
            Arrays::each($rows, fn ($row) => $row->link());
            $this->appendToJson("rows", Arrays::orderBy($rows, "_orderfield"));
        } else if ($view == "form") $this->appendToJson(['fields'], Arrays::firstOrNull((new Library)->get($id)));
        $this->handle();
    }

    public function getAvailableBooks($view, $id = null) {
        if ($view == "select") {
            $schoolId = Helpers::input()->get("parentValue");
            $this->appendToJson("items", (new Library)->getByAvailableBySchoolId($schoolId));
        }
        $this->handle();
    }

    public function getLendBooks($view, $id = null)
    {
        if ($view == "select") {
            $schoolId = Helpers::input()->get("parentValue");
            $this->appendToJson("items", (new Library)->getByLendBySchoolId($schoolId));
        }
        $this->handle();
    }

    public function getBookAction($view, $id = null)
    {
        $actions = (new LibraryAction)->getByBookId($id);

        if ($actions == []) {
            $this->appendToJson(["html"], "Dit boek is tot op heden nog niet uitgeleend.");
        }

        if ($view == 'html') {
            $html = "";

            foreach ($actions as $action) {
                $action->link();
                $template = self::TEMPLATE_ACTIONS;

                foreach ($action->toArray() as $key => $value) $template = str_replace("{{action:{$key}}}", $value, $template);
                $html .= $template;
            }

            $this->appendToJson(["html"], $html);
        }
        $this->handle();
    }

    // POST
    public function postBook($view, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')?->getValue();
        $author = Helpers::input()->post('author')?->getValue();
        $title = Helpers::input()->post('title')?->getValue();
        $isdn = Helpers::input()->post('isdn')?->getValue();
        $category = Helpers::input()->post('category')?->getValue();
        $faction = Helpers::input()->post('faction', false)->getValue();

        $schoollibraryLibraryRepo = new Library;

        if ($faction !== "delete") {
            if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
                $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
            }
            if (!Input::check($author) || Input::empty($author)) {
                $this->setValidation("auteur", "Auteur moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                Log::write(type: Log::TYPE_ERROR, description: "Author is not filled in");
            }
            if (!Input::check($title) || Input::empty($title)) {
                $this->setValidation("titel", "Titel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                Log::write(type: Log::TYPE_ERROR, description: "Title is not filled in");
            }
            /*if (!Input::check($isdn) || Input::empty($isdn)) {
                $this->setValidation("isdn", "ISDN moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                Log::write(type: Log::TYPE_ERROR, description: "Isdn is not filled in");
            }*/
            if (!Input::check($category) || Input::empty($category)) {
                $this->setValidation("categorie", "Categorie moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
                Log::write(type: Log::TYPE_ERROR, description: "Category is not filled in");
            }

            if ($this->validationIsAllGood()) {
                $book = is_null($id) ? new ObjectLibrary() : $schoollibraryLibraryRepo->get($id)[0];
                $exBooks = $schoollibraryLibraryRepo->checkAlreadyExist($schoolId, $author, $title, $isdn, $category, $id);
                $now = Clock::nowAsString("Y-m-d H:i:s");

                if (!empty($exBooks)) {
                    $book = Arrays::firstOrNull($exBooks);
                    $book->numberOfAvailableCopies += 1;
                    $book->numberOfCopies += 1;
                    $book->lastActionDateTime = $now;
                    $schoollibraryLibraryRepo->set($book);
                    $this->setToast("Schoolbibliotheek - Bibliotheek", "Het boek met titel \"{$book->title}\" bestaat al in de bibliotheek dus is er één extra exemplaar toegevoegd.");
                    Log::write(description: "This book with bookId \"{$book->id}\" and title \"{$book->title}\" already exists in the library, so one extra copy has been added");
                } else {
                    $book->schoolId = $schoolId;
                    $book->author = $author;
                    $book->title = $title;
                    $book->isdn = $isdn;
                    $book->category = $category;
                    $book->lastActionDateTime = $now;
                    $newBook = $schoollibraryLibraryRepo->set($book);
                    $this->setToast("Schoolbibliotheek - Bibliotheek", "Het boek met titel \"{$book->title}\" is opgeslagen in de bibliotheek.");
                    Log::write(description: "Added/Edited book \"{$book->title}\" with id \"" . (is_null($id) ? $newBook : $id) . "\" to the schoollibrary");
                }
            }
        } else {
            $ids = Helpers::input()->post('ids')->getValue();
            $ids = explode("-", $ids);

            foreach ($ids as $_id) {
                $book = Arrays::firstOrNull($schoollibraryLibraryRepo->get($_id));

                if ($book->numberOfAvailableCopies == 0) {
                    $this->setToast("Schoolbibliotheek - Bibliotheek", "Het boek met titel \"{$book->title}\" kan niet verwijderd worden omdat het nog uitgeleend is.", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "The book with title \"{$book->title}\" and id \"{$book->id}\" can not be deleted because it is still on loan");
                } else if (!is_null($book)) {
                    if ($book->numberOfCopies >= 2) {
                        $book->numberOfCopies -= 1;
                        $book->numberOfAvailableCopies -= 1;
                        $schoollibraryLibraryRepo->set($book);
                        Log::write(description: "Number of copies of the book with title \"{$book->title}\" with id \"{$book->id}\" is down with one");
                        $this->setToast("Schoolbibliotheek - Bibliotheek", "Het boek met titel \"{$book->title}\" is met één exemplaar verminderd.");
                    } else if ($book->numberOfCopies == 1) {
                        $book->deleted = 1;
                        $schoollibraryLibraryRepo->set($book);
                        $this->setToast("Schoollibrary - Library", "Het boek met titel \"{$book->title}\" is verwijderd!");
                        Log::write(description: "Deleted book with title \"{$book->title}\" and id \"{$book->id}\"");
                    }
                }
            }
        }

        if (!$this->validationIsAllGood()) $this->setHttpCode(400);
        else {
            $this->setCloseModal();
            $this->setReloadTable();
        }
        $this->handle();
    }

    public function postLendBook($view, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')?->getValue();
        $studentOrStaff = Helpers::input()->post('studentOrStaff')?->getValue();
        $personId = Helpers::input()->post('personId')?->getValue();
        $categoryId = Helpers::input()->post('categoryId')?->getValue();
        $bookId = Helpers::input()->post('bookId')?->getValue();

        $schoolLibraryLendRepo = new Library;
        $schoolLibraryActionRepo = new LibraryAction;
        $schoolRepo = new School;
        if ($studentOrStaff == "student") {
            $informatRepo = new InformatStudent;
        } else {
            $informatRepo = new InformatStaff;
        }

        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
            $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
        }
        if (!Input::check($studentOrStaff) || Input::empty($studentOrStaff)) {
            $this->setValidation("studentOrStaff", "Student of personeel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "Student or staff is not filled in");
        }
        if (!Input::check($personId, Input::INPUT_TYPE_INT) || Input::empty($personId)) {
            $this->setValidation("personId", "Ontlenende moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "Lend person is not filled in");
        }
        if (!Input::check($bookId) || Input::empty($bookId)) {
            $this->setValidation("bookId", "BookId moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "BookId is not filled in");
        }

        if ($this->validationIsAllGood()) {
            $now = Clock::nowAsString("Y-m-d H:i:s");
            $localUser = User::getLoggedInUser();

            foreach (explode(";", $bookId) as $id) {
                $book = $schoolLibraryLendRepo->getByBookId($id)[0];
                $school = $schoolRepo->get($book->schoolId)[0];
                $person = $informatRepo->get($personId)[0];

                if ($book->numberOfAvailableCopies == 0) {
                    $this->setToast("Schoolbibliotheek - Uitlenen", "Het boek met titel \"{$book->title}\" is niet uitgeleend aangezien deze niet meer beschikbaar is in de bibliotheek", self::VALIDATION_STATE_INVALID);
                    Log::write(type: Log::TYPE_ERROR, description: "The book with title \"{$book->title}\" and id \"{$book->id}\" can not be lend out because it has no available copies in the library");
                } else {
                    $book->numberOfAvailableCopies -= 1;
                    $schoolLibraryLendRepo->set($book);
                    $schoolLibraryActionRepo->set(new ObjectLibraryAction([
                        'bookId' => $id,
                        'creationDateTime' => $now,
                        'description' => "\"{$localUser->fullName}\" heeft het boek met titel \"{$book->title}\" uitgeleend aan \"{$person->fullName}\". Dit is een " . ($studentOrStaff == "staff" ? "personeelslid" : "leerling") . " van de school \"{$school->name}\"."
                    ]));
                    $this->setToast("Schoolbibliotheek - Uitlenen", "Het boek met titel \"{$book->title}\" is uitgeleend.");
                    Log::write(description: "The book with title \"{$book->title}\" and id \"{$book->id}\" is succesfully lend out");
                }
            }
        }

        if (!$this->validationIsAllGood()) {
            $this->setHttpCode(400);
        } else {
            $this->setResetForm();
            $this->setReloadTable();
            $this->setCloseModal();
        }

        $this->handle();
    }

    public function postReturnBook($view, $id = null)
    {
        $schoolId = Helpers::input()->post('schoolId')?->getValue();
        $studentOrStaff = Helpers::input()->post('studentOrStaff')?->getValue();
        $personId = Helpers::input()->post('personId')?->getValue();

        $bookId = Helpers::input()->post('bookId')?->getValue();

        $schoolLibraryReturnRepo = new Library;
        $schoolRepo = new School;
        $schoolLibraryActionRepo = new LibraryAction;
        if ($studentOrStaff == "student") {
            $informatRepo = new InformatStudent;
        } else {
            $informatRepo = new InformatStaff;
        }

        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) {
            $this->setValidation("schoolId", "School moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "School is not filled in");
        }
        if (!Input::check($studentOrStaff) || Input::empty($studentOrStaff)) {
            $this->setValidation("studentOrStaff", "Student of personeel moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "Student or staff is not filled in");
        }
        if (!Input::check($personId, Input::INPUT_TYPE_INT) || Input::empty($personId)) {
            $this->setValidation("personId", "Ontlenende moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "Lend person is not filled in");
        }
        if (!Input::check($bookId) || Input::empty($bookId)) {
            $this->setValidation("bookId", "BookId moet ingevuld zijn!", self::VALIDATION_STATE_INVALID);
            Log::write(type: Log::TYPE_ERROR, description: "BookId is not filled in");
        }

        if ($this->validationIsAllGood()) {
            $now = Clock::nowAsString("Y-m-d H:i:s");
            $localUser = User::getLoggedInUser();

            foreach (explode(";", $bookId) as $id) {
                $book = $schoolLibraryReturnRepo->getByBookId($id)[0];
                $school = $schoolRepo->get($book->schoolId)[0];
                $person = $informatRepo->get($personId)[0];

                $book->numberOfAvailableCopies += 1;
                $schoolLibraryReturnRepo->set($book);
                $schoolLibraryActionRepo->set(new ObjectLibraryAction([
                    'bookId' => $id,
                    'creationDateTime' => $now,
                    'description' => "\"{$person->fullName}\" heeft het boek met titel \"{$book->title}\" teruggebracht aan \"{$localUser->fullName}\" in de school \"{$school->name}\"."
                ]));
                $this->setToast("Schoolbibliotheek - Terugbrengen", "Het boek met titel \"{$book->title}\" is teruggebracht.");
                Log::write(description: "The book with title \"{$book->title}\" and id \"{$book->id}\" is succesfully returned");
            }
        }

        if (!$this->validationIsAllGood()) {
            $this->setHttpCode(400);
        } else {
            $this->setResetForm();
            $this->setReloadTable();
            $this->setCloseModal();
        }

        $this->handle();
    }
}
