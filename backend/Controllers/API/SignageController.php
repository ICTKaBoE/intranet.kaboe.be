<?php

namespace Controllers\API;

use ClanCats\Hydrahon\Query\Sql\Replace;
use Router\Helpers;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Controllers\ApiController;
use Database\Object\Signage\Group as SignageGroup;
use Database\Repository\Library\Book;
use Database\Repository\Signage\Media;
use Database\Repository\Library\Author;
use Database\Object\Signage\Media as SignageMedia;
use Database\Object\Signage\Playlist as SignagePlaylist;
use Database\Object\Signage\PlaylistItem as SignagePlaylistItem;
use Database\Object\Signage\Screen as SignageScreen;
use Database\Repository\Signage\Group;
use Database\Repository\Signage\Playlist;
use Database\Repository\Signage\PlaylistItem;
use Database\Repository\Signage\Screen;
use getID3;

class SignageController extends ApiController
{
    // Get Functions
    protected function getList($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SIGNAGE)) {
            $screenRepo = new Screen;
            if (!Helpers::url()->hasParam("code")) $this->setRedirect("https://dev.extranet.kaboe.be/signage/register?code=" . General::generateCode());
            else if (!$screenRepo->getByCode(Helpers::url()->getParam("code"))) $this->setRedirect("https://dev.extranet.kaboe.be/signage/notfound?code=" . Helpers::url()->getParam("code"));
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
        $filters = [
            'schoolId' => Helpers::url()->getParam('schoolId')
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"]]);
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
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Toegewezen aan",
                        "data" => "formatted.assignedTo",
                        "width" => "300px"
                    ],
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

    protected function getPlaylistItem($view, $id = null)
    {
        $playlistRepo = new Playlist;
        $repo = new PlaylistItem;

        $playlist = Arrays::firstOrNull($playlistRepo->get(Helpers::url()->getParam("playlistId")));

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"]]);
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
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', Arrays::firstOrNull($repo->get($id)));
    }

    protected function getScreen($view, $id = null)
    {
        $repo = new Screen;
        $filters = [
            'schoolId' => Helpers::url()->getParam('schoolId')
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"]]);
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
                        "title" => "Naam",
                        "data" => "name"
                    ],
                    [
                        "title" => "Code",
                        "data" => "code",
                        "width" => "200px"
                    ],
                    [
                        "title" => "Groep",
                        "data" => "linked.group.name",
                        "width" => "200px",
                        "defaultContent" => ""
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

    protected function getGroup($view, $id = null)
    {
        $repo = new Group;
        $filters = [
            'schoolId' => Helpers::url()->getParam('schoolId')
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"]]);
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

    protected function getMedia($view, $id = null)
    {
        $repo = new Media;
        $filters = [
            'schoolId' => Helpers::url()->getParam('schoolId')
        ];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $this->appendToJson("checkbox", true);
            $this->appendToJson("defaultOrder", [[3, "asc"]]);
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
                        "title" => "Type",
                        "data" => "type",
                        "width" => "100px"
                    ],
                    [
                        "title" => "Alias",
                        "data" => "alias"
                    ],
                    [
                        "title" => "Duur",
                        "data" => "length",
                        "width" => "100px"
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

    // Post functions
    protected function postPlaylist($view, $id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $assignedTo = Helpers::input()->post('assignedTo')->getValue();
        $assignedToId = Helpers::input()->post('assignedToId')->getValue();

        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($assignedTo) || Input::empty($assignedTo)) $this->setValidation("assignedTo", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($assignedToId) || Input::empty($assignedToId)) $this->setValidation("assignedToId", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Playlist;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new SignagePlaylist;
                $item->schoolId = $schoolId;
                $item->name = $name;
                $item->assignedTo = $assignedTo;
                $item->assignedToId = $assignedToId;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De playlist is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postPlaylistItem($view, $id = null)
    {
        if ($id == "add") $id = null;

        $playlistId = Helpers::input()->post('playlistId')->getValue();
        $mediaId = Helpers::input()->post("mediaId")->getValue();
        $duration = Helpers::input()->post("duration")->getValue();

        if (!Input::check($mediaId, Input::INPUT_TYPE_INT) || Input::empty($mediaId)) $this->setValidation("mediaId", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new PlaylistItem;
            $playlist = Arrays::firstOrNull((new Playlist)->get($playlistId));
            $media = Arrays::firstOrNull((new Media)->get($mediaId));

            $item = $id ? Arrays::first($repo->get($id)) : new SignagePlaylistItem;
            $item->playlistId = $playlist->id;
            $item->mediaId = $mediaId;
            $item->duration = $media->type == "V" ? $media->duration : $duration;
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
            $origItem = Arrays::firstOrNull($repo->get($_id));
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
            $origItem = Arrays::firstOrNull($repo->get($_id));
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

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();
        $code = Helpers::input()->post('code')->getValue();
        $groupId = Helpers::input()->post('groupId')->getValue();

        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($code) || Input::empty($code)) $this->setValidation("code", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Screen;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new SignageScreen;
                $item->schoolId = $schoolId;
                $item->name = $name;
                $item->code = $code;
                $item->groupId = $groupId;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het scherm is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postGroup($view, $id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $name = Helpers::input()->post('name')->getValue();

        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($name) || Input::empty($name)) $this->setValidation("name", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Group;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new SignageGroup;
                $item->schoolId = $schoolId;
                $item->name = $name;

                $repo->set($item);
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("De groep is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postMedia($view, $id = null)
    {
        if ($id == "add") $id = null;

        $schoolId = Helpers::input()->post('schoolId')->getValue();
        $type = Helpers::input()->post('type')->getValue();
        $alias = Helpers::input()->post('alias')->getValue();
        $mediaImage = Helpers::input()->file('mediaImage');
        $mediaVideo = Helpers::input()->file('mediaVideo');
        $mediaLink = Helpers::input()->post('mediaLink');

        if (!Input::check($alias) || Input::empty($alias)) $this->setValidation("alias", state: self::VALIDATION_STATE_INVALID);
        if (!Input::check($schoolId, Input::INPUT_TYPE_INT) || Input::empty($schoolId)) $this->setValidation("schoolId", state: self::VALIDATION_STATE_INVALID);
        if (Strings::equal($type, 'I') && $mediaImage[0]->getSize() == 0) $this->setValidation("mediaImage", state: self::VALIDATION_STATE_INVALID);
        if (Strings::equal($type, 'V') && $mediaVideo[0]->getSize() == 0) $this->setValidation("mediaVideo", state: self::VALIDATION_STATE_INVALID);
        if (Strings::equal($type, 'L') && (!Input::check($mediaLink) || Input::empty($mediaLink))) $this->setValidation("mediaLink", state: self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $repo = new Media;

            if ($this->validationIsAllGood()) {
                $item = $id ? Arrays::first($repo->get($id)) : new SignageMedia;
                $item->schoolId = $schoolId;
                $item->type = $type;
                $item->alias = $alias;
                $item->link = $mediaLink;

                $nId = $repo->set($item);
                if (!$id) $item = Arrays::first($repo->get($nId));

                if (Arrays::contains(['I', 'V'], $type)) {
                    $file = $type == 'I' ? $mediaImage[0] : $mediaVideo[0];
                    if ($file && $file->getSize() > 0) {
                        FileSystem::CreateFolder(LOCATION_UPLOAD . "/signage");
                        $newName = $item->guid . "." . $file->getExtension();

                        if ($file->move(LOCATION_UPLOAD . "/signage/{$newName}")) {
                            $item->link = $newName;
                            $item->size = $file->getSize();
                            $item->length = (new getID3)->analyze(LOCATION_UPLOAD . "/signage/{$newName}")["playtime_string"];
                            $item->duration = (new getID3)->analyze(LOCATION_UPLOAD . "/signage/{$newName}")["playtime_seconds"];
                            $repo->set($item);
                        }
                    }
                }
            }
        }

        if ($this->validationIsAllGood()) {
            $this->setToast("Het media is opgeslagen!");
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    // Delete functions    
    protected function deletePlaylist($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Playlist;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));
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
            $item = Arrays::first($repo->get($_id));
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
            $item = Arrays::first($repo->get($_id));
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
            $item = Arrays::first($repo->get($_id));
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
            $item = Arrays::first($repo->get($_id));

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
