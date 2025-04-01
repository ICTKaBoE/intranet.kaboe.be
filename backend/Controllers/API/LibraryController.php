<?php

namespace Controllers\API;

use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\Session;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Navigation;
use Database\Repository\Library\Book;
use Database\Repository\Library\Author;
use Database\Repository\Library\Category;
use Database\Object\Library\Book as LibraryBook;
use Database\Object\Library\Author as LibraryAuthor;
use Database\Object\Library\BookHistory as LibraryBookHistory;
use Database\Object\Library\Category as LibraryCategory;
use Database\Repository\Library\BookHistory;
use Ouzo\Utilities\Clock;
use Security\User;

class LibraryController extends ApiController
{
    // Get Functions
    protected function getAuthor($view, $id = null)
    {
        $repo = new Author;
        $filters = [];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "type" => "checkbox",
                        "data" => null,
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "20px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getCategory($view, $id = null)
    {
        $repo = new Category;
        $filters = [];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "type" => "checkbox",
                        "data" => null,
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "20px"
                    ],
                    [
                        "title" => "Naam",
                        "data" => "name"
                    ]
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getBook($view, $id = null)
    {
        $repo = new Book;
        $filters = [
            'schoolId' => Arrays::filter(explode(";", Helpers::url()->getParam('schoolId')), fn($i) => Strings::isNotBlank($i)),
            'authorId' => Arrays::filter(explode(";", Helpers::url()->getParam('authorId')), fn($i) => Strings::isNotBlank($i)),
            'categoryId' => Arrays::filter(explode(";", Helpers::url()->getParam('categoryId')), fn($i) => Strings::isNotBlank($i)),
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[1, "asc"], [2, "asc"], [3, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "type" => "checkbox",
                        "data" => null,
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "20px"
                    ],
                    [
                        "title" => "School",
                        "data" => "linked.school.formatted.badge.name",
                        "orderable" => false,
                        "searchable" => false,
                        "width" => "100px"
                    ],
                    [
                        "title" => "Categorie",
                        "data" => "linked.category.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Auteur",
                        "data" => "linked.author.name",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Titel",
                        "data" => "title"
                    ],
                    [
                        "title" => "#",
                        "data" => "formatted.free",
                        "width" => "50px"
                    ],
                    [
                        "title" => "Momenteel uitgeleend aan",
                        "data" => "lendTo",
                        "width" => "300px"
                    ]
                ]
            );

            $items = $repo->get();
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getBookHistory($view, $id = null)
    {
        $book = Arrays::firstOrNull((new Book)->get($id));
        $repo = new BookHistory;
        $filters = [];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", false);
            $this->appendToJson("defaultOrder", [[3, "desc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [
                    [
                        "title" => "Boek",
                        "data" => "linked.book.formatted.nameWithAuthor"
                    ],
                    [
                        "title" => "Uitgeleend door",
                        "data" => "linked.lenderUser.formatted.fullName",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Aan",
                        "data" => "linked.lender.formatted.fullName",
                        "width" => "150px"
                    ],
                    [
                        "title" => "Op",
                        "data" => "formatted.lendAt",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Teruggebracht door",
                        "data" => "linked.returner.formatted.fullName",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Bij",
                        "data" => "linked.receiverUser.formatted.fullName",
                        "width" => "150px",
                        "defaultContent" => ""
                    ],
                    [
                        "title" => "Op",
                        "data" => "formatted.returnedAt",
                        "width" => "150px",
                        "defaultContent" => ""
                    ]
                ]
            );

            $items = $repo->getByBookId($book->id);
            General::filter($items, $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get($id);
            General::filter($items, $filters);

            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getType($view, $id = null)
    {
        $settings = Arrays::first((new Navigation)->get(Session::get("moduleSettingsId")))->settings;
        $statuses = $settings['type'];

        if (Strings::equal($view, self::VIEW_SELECT)) {
            $_statuses = [];

            foreach ($statuses as $k => $v) $_statuses[] = ["id" => $k, ...$v];

            $this->appendToJson('items', $_statuses);
        }
    }

    // Post functions
    protected function postAuthor($view, $id = null)
    {
        if ($id == "add") $id = null;

        $name = Helpers::input()->post('name')->getValue();

        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Author;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new LibraryAuthor;
                $item->name = $name;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De auteur is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postCategory($view, $id = null)
    {
        if ($id == "add") $id = null;

        $name = Helpers::input()->post('name')->getValue();

        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Category;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new LibraryCategory;
                $item->name = $name;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De categorie is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postBook($view, $id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $authorId = Helpers::input()->post('authorId')->getValue();
        $categoryId = Helpers::input()->post('categoryId')->getValue();
        $title = Helpers::input()->post('title')->getValue();

        if (!Input::check($schoolId) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($authorId) || Input::empty($authorId)) $this->setValidation("authorId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($categoryId) || Input::empty($categoryId)) $this->setValidation("categoryId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($title) || Input::empty($title)) $this->setValidation("title", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Book;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new LibraryBook;
                $item->schoolId = $schoolId;
                $item->authorId = $authorId;
                $item->categoryId = $categoryId;
                $item->title = $title;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het boek is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postBookLend($view, $id = null)
    {
        if (!$id) $this->setToast("Er is geen boek gelesecteerd!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $lenderType = Helpers::input()->post('lenderType')->getValue();
            $lenderInformatId = Helpers::input()->post('lenderInformatId')->getValue();

            if (!Input::check($lenderType) || Input::empty($lenderType)) $this->setValidation("lenderType", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($lenderInformatId) || Input::empty($lenderInformatId)) $this->setValidation("lenderInformatId", state: self::VALIDATION_STATE_INVALID);

            if ($this->validationIsAllGood()) {
                $bookRepo = new Book;
                $bookHistoryRepo = new BookHistory;

                $id = explode("_", $id);

                foreach ($id as $_id) {
                    $book = Arrays::first($bookRepo->get($_id));

                    if ($book->free == 0) {
                        $this->setToast("Het boek '{$book->title}' kan niet worden uitgeleend wegens niet meer vrij!", self::VALIDATION_STATE_INVALID);
                        continue;
                    }

                    $lend = new LibraryBookHistory;
                    $lend->bookId = $book->id;
                    $lend->lenderUserId = User::getLoggedInUser()->id;
                    $lend->lenderType = $lenderType;
                    $lend->lenderInformatId = $lenderInformatId;
                    $bookHistoryRepo->set($lend);
                    $lend->reinit();

                    $book->free = $book->free - 1;
                    $book->lendTo = $lend->linked->lender->formatted->fullNameReversed;
                    $bookRepo->set($book);
                    $this->setToast("Het boek '{$book->title}' is uitgeleend aan '{$lend->linked->lender->formatted->fullNameReversed}'!");
                }

                $this->setCloseModal();
                $this->setReloadTable();
            } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
        }
    }

    protected function postBookReturn($view, $id = null)
    {
        if (!$id) $this->setToast("Er is geen boek gelesecteerd!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $returnerType = Helpers::input()->post('returnerType')->getValue();
            $returnerInformatId = Helpers::input()->post('returnerInformatId')->getValue();

            if (!Input::check($returnerType) || Input::empty($returnerType)) $this->setValidation("returnerType", state: self::VALIDATION_STATE_INVALID);
            if (!Input::check($returnerInformatId) || Input::empty($returnerInformatId)) $this->setValidation("returnerInformatId", state: self::VALIDATION_STATE_INVALID);

            if ($this->validationIsAllGood()) {
                $bookRepo = new Book;
                $bookHistoryRepo = new BookHistory;

                $id = explode("_", $id);

                foreach ($id as $_id) {
                    $book = Arrays::first($bookRepo->get($_id));

                    if ($book->free == $book->amount) {
                        $this->setToast("Het boek '{$book->title}' kan niet worden teruggebracht wegens niet uitgeleend!", self::VALIDATION_STATE_INVALID);
                        continue;
                    }

                    $return = Arrays::firstOrNull($bookHistoryRepo->getNotReturnedByBookId($book->id));

                    if (!$return) {
                        $this->setToast("Er is geen uitlening teruggevonden voor het boek '{$book->title}'!", self::VALIDATION_STATE_INVALID);
                        continue;
                    }

                    $return->receiverUserId = User::getLoggedInUser()->id;
                    $return->returnerType = $returnerType;
                    $return->returnerInformatId = $returnerInformatId;
                    $return->returnDateTime = Clock::nowAsString("Y-m-d H:i:s");
                    $bookHistoryRepo->set($return);
                    $return->reinit();

                    $book->free = $book->free + 1;
                    $book->lendTo = null;
                    $bookRepo->set($book);
                    $this->setToast("Het boek '{$book->title}' is teruggebracht door '{$return->linked->returner->formatted->fullNameReversed}'!");
                }

                $this->setCloseModal();
                $this->setReloadTable();
            } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
        }
    }

    // Delete functions    
    protected function deleteAuthor($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Author;
        $bookRepo = new Book;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));

            if (count($bookRepo->getByAuthorId($item->id))) {
                $this->setToast("De auteur '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan boeken!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De auteur '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteCategory($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Category;
        $bookRepo = new Book;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));

            if (count($bookRepo->getByCategoryId($item->id))) {
                $this->setToast("De categorie '{$item->name}' kan niet worden verwijderd!<br />Deze is gekoppeld aan boeken!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De categorie '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteBook($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Book;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het boek '{$item->title}' is verwijderd!");
        }

        $this->setReloadTable();
    }
}
