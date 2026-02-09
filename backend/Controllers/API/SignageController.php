<?php

namespace Controllers\API;

use getID3;
use Helpers\Form;
use Helpers\Table;
use Helpers\Filter;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Repository\Signage\Group;
use Database\Repository\Signage\Media;
use Database\Repository\Signage\Screen;
use Database\Repository\Signage\Playlist;
use Database\Repository\Signage\PlaylistItem;
use Database\Object\Signage\Group as SignageGroup;
use Database\Object\Signage\Media as SignageMedia;
use Database\Object\Signage\Screen as SignageScreen;
use Database\Object\Signage\Playlist as SignagePlaylist;
use Database\Object\Signage\PlaylistItem as SignagePlaylistItem;

class SignageController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "signage";

    // Get Functions
    protected function getList($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SIGNAGE)) {
            $screenRepo = new Screen;
            if (!Helpers::url()->hasParam("code")) $this->setRedirect("/register?code=" . General::generateCode());
            else if (!$screenRepo->getByCode(Helpers::url()->getParam("code"))) $this->setRedirect("/notfound?code=" . Helpers::url()->getParam("code"));
            else {
                $screen = $screenRepo->getByCode(Helpers::url()->getParam("code"));
                $playlist = (new Playlist)->getByAssignedToAndAssignedToId(is_null($screen->linked->group) ? "S" : "G", is_null($screen->linked->group) ? $screen->id : $screen->groupId);
                $items = (new PlaylistItem)->getByPlaylistId($playlist->id);
                $this->appendToJson('items', $items);
            }
        }
    }

    protected function getPlaylist($view, $id = null)
    {
        $repo = new Playlist;
        $filters = Filter::Find(['schoolId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getPlaylistItem($view, $id = null)
    {
        $playlistRepo = new Playlist;
        $repo = new PlaylistItem;

        $playlist = Arrays::firstOrNull($playlistRepo->get(Helpers::url()->getParam("playlistId")));

        if (Strings::equal($view, self::VIEW_TABLE)) {

            $this->appendToJson("defaultOrder", [[3, "asc"]]);
            $this->appendToJson(
                key: 'columns',
                data: [

                    [
                        "title" => "Naam",
                        "data" => "linked.media.alias"
                    ],
                    [
                        "title" => "Speelduur",
                        "data" => "formatted.duration",
                        "width" => "100px"
                    ]
                ]
            );

            $items = $repo->getByPlaylistId($playlist->id);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getScreen($view, $id = null)
    {
        $repo = new Screen;
        $filters = Filter::Find(['schoolId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getGroup($view, $id = null)
    {
        $repo = new Group;
        $filters = Filter::Find(['schoolId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getMedia($view, $id = null)
    {
        $repo = new Media;
        $filters = Filter::Find(['schoolId']);

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get(filters: $filters);
            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $items = $repo->get(filters: $filters);
            $this->appendToJson('items', Arrays::map($items, fn($i) => $i->toArray(true)));
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    // Post functions
    protected function postPlaylist($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "assignedTo" => ["mandatory" => true],
            "assignedToId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Playlist;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SignagePlaylist;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postPlaylistItem($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "playlistId" => ["mandatory" => true],
            "mediaId" => ["mandatory" => true],
            "duration" => ["mandatory" => true]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new PlaylistItem;
            $playlist = (new Playlist)->getById($fields["playlistId"]);
            $media = (new Media)->getById($fields["mediaId"]);

            $item = $repo->getById($id) ?? new SignagePlaylistItem;
            $item->fillWithPostData();
            $item->duration = $media->type == "V" ? $media->duration : $fields["duration"];
            $item->order = $id ? $item->order : count($repo->getByPlaylistId($playlist->id)) + 1;

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De playlist is opgeslagen!");
            $this->setReloadTable();
            $this->setCloseModal();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postPlaylistItemUp($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new PlaylistItem;

        foreach ($id as $_id) {
            $origItem = $repo->getById($_id);
            $replaceItem = Arrays::firstOrNull(Arrays::filter($repo->getByPlaylistId($origItem->playlistId), fn($ri) => $ri->order == $origItem->order - 1));
            if (!$replaceItem) continue;

            $origItem->order -= 1;
            $repo->set($origItem);

            $replaceItem->order += 1;
            $repo->set($replaceItem);
        }

        $this->setReloadTable();
    }

    protected function postPlaylistItemDown($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new PlaylistItem;

        foreach ($id as $_id) {
            $origItem = $repo->getById($_id);
            $replaceItem = Arrays::firstOrNull(Arrays::filter($repo->getByPlaylistId($origItem->playlistId), fn($ri) => $ri->order == $origItem->order + 1));
            if (!$replaceItem) continue;

            $origItem->order += 1;
            $repo->set($origItem);

            $replaceItem->order -= 1;
            $repo->set($replaceItem);
        }

        $this->setReloadTable();
    }

    protected function postScreen($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
            "code" => ["mandatory" => true],
            "groupId" => ["type" => Input::INPUT_TYPE_INT]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Screen;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SignageScreen;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postGroup($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Group;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SignageGroup;
                $item->fillWithPostData();

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postMedia($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "schoolId" => ["mandatory" => true, "type" => Input::INPUT_TYPE_INT],
            "type",
            "alias" => ["mandatory" => true],
            "mediaImage" => ["mandatory" => true, "type" => "file", "preconditions" => ["type" => "I"]],
            "mediaVideo" => ["mandatory" => true, "type" => "file", "preconditions" => ["type" => "V"]],
            "mediaLink" => ["mandatory" => true, "preconditions" => ["type" => "L"]]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Media;

            if ($this->validationIsAllGood()) {
                $item = $repo->getById($id) ?? new SignageMedia;
                $item->fillWithPostData();

                $nId = $repo->set($item);
                if (!$id) $item = $repo->getById($nId);

                if (Arrays::contains(['I', 'V'], $fields["type"])) {
                    $file = $fields["type"] == 'I' ? $fields["mediaImage"][0] : $fields["mediaVideo"][0];
                    if ($file && $file->getSize() > 0) {
                        FileSystem::CreateFolder(LOCATION_FILES . "/signage");
                        $newName = $item->guid . "." . $file->getExtension();

                        if ($file->move(LOCATION_FILES . "/signage/{$newName}")) {
                            $item->link = $newName;
                            $item->size = $file->getSize();
                            $item->length = (new getID3)->analyze(LOCATION_FILES . "/signage/{$newName}")["playtime_string"];
                            $item->duration = (new getID3)->analyze(LOCATION_FILES . "/signage/{$newName}")["playtime_seconds"];
                            $repo->set($item);
                        }
                    }
                }
            }
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Delete functions    
    protected function deletePlaylist($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Playlist;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De playlist '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deletePlaylistItem($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new PlaylistItem;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het playlist-item is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteScreen($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Screen;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het scherm '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteGroup($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Group;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);
            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De groep '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    protected function deleteMedia($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Media;
        $playlistItemRepo = new PlaylistItem;

        foreach ($id as $_id) {
            $item = $repo->getById($_id);

            if (count($playlistItemRepo->getByMediaId($item->id))) {
                $this->setToast("Het media '{$item->alias}' kan niet worden verwijderd!<br />Deze is gekoppeld aan playlists!", self::VALIDATION_STATE_INVALID);
                continue;
            }

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("Het media '{$item->alias}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
}
