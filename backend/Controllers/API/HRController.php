<?php

namespace Controllers\API;

use Controllers\ApiController;
use Database\Object\HR\History as HRHistory;
use Database\Object\HR\HR as HRHR;
use Database\Object\HR\Role as HRRole;
use Database\Object\Mail\Mail as ObjectMail;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Object\Navigation\TableDef;
use Database\Repository\HR\History;
use Database\Repository\HR\HR;
use Database\Repository\HR\Role;
use Database\Repository\HR\Status;
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Database\Repository\Navigation\Setting;
use Database\Repository\School\School;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\Table;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Router\Helpers;
use Security\FileSystem;
use Security\User;
use stdClass;

class HRController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "hr";

    // Get functions
    protected function getOverview($view, $id = null)
    {
        $repo = new HR;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find(['schoolId', 'statusId']);

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getOverviewHistory($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new History;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format([
                new TableDef([
                    "order" => 1,
                    "title" => "Datum/Tijd",
                    "data" => "formatted.datetime",
                    "orderable" => false,
                    "searchable" => false,
                    "render" => true,
                    "width" => 200
                ]),
                new TableDef([
                    "order" => 2,
                    "title" => "Actie",
                    "data" => "formatted.action",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 100
                ]),
                new TableDef([
                    "order" => 3,
                    "title" => "Door",
                    "data" => "linked.creatorUser.formatted.fullNameReversed",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 200
                ]),
                new TableDef([
                    "order" => 4,
                    "title" => "Oude gegevens",
                    "data" => "formatted.changedData",
                    "orderable" => false,
                    "searchable" => false,
                    "width" => 0
                ]),
            ], false);
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            if ($id) {
                $items = $repo->getByHrId((new HR)->getById($id)->id);
                $this->appendToJson("rows", $items);
            }
        } else if (Strings::equal($view, self::VIEW_LIST)) {
            if (!is_null($id)) {
                $items = $repo->getByHrId((new HR)->getById($id)->id);
                $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "@"));
            } else $this->appendToJson('raw', 'Geen geschiedenis');
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getStatus($view, $id = null)
    {
        if (Strings::equal($view, self::VIEW_SELECT)) {
            $this->appendToJson('items', (new Status)->get());
        }
    }

    protected function getRole($view, $id = null)
    {
        $repo = new Role;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = Filter::Find();

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) {
            $optgroups = (new School)->get();
            $items = $repo->get();
            Arrays::each($items, fn($i) => $i->optgroup = $i->schoolId);
            // $items[] = ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE];

            $this->appendToJson('optgroups', $optgroups);
            $this->appendToJson('items', $items);
        } else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    protected function getSex($view, $id = null)
    {
        $items = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "sex")->value;
        $items = explode(",", $items);
        $items = Arrays::map($items, fn($i) => explode(":", $i));
        $items = Arrays::map($items, fn($i) => ["id" => $i[0], "name" => $i[1]]);
        $this->appendToJson('items', $items);
    }

    protected function getLaptopUsage($view, $id = null)
    {
        $items = (new Setting)->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "laptopUsage")->value;
        $items = explode(",", $items);
        $items = Arrays::map($items, fn($i) => explode(":", $i));
        $items = Arrays::map($items, fn($i) => ["id" => $i[0], "name" => $i[1]]);
        $this->appendToJson('items', $items);
    }

    protected function getFlow($view, $id = null)
    {
        $settings = $this->getSettings(true);
        $settings = Arrays::filterByKeys($settings, fn($k) => Strings::startsWith($k, 'flow.'));
        $this->appendToJson('fields', Arrays::flattenKeysRecursively($settings));
    }

    protected function getParameter($view, $id = null)
    {
        $items = [];
        foreach (HRHR::PARAMETERS as $k => $v) $items[] = ["id" => $k, "name" => $v];
        $this->appendToJson('items', $items);
    }

    // Post functions
    protected function postOverview($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new HR;
        $historyRepo = new History;

        $_fields = [
            "name" => ["mandatory" => true],
            "firstName" => ["mandatory" => true],
            "sex" => ["mandatory" => true],
            "start" => ["mandatory" => true],
            "end" => ["default" => null, "trimToNull" => true],
            "informatId" => ["default" => null],
            "insz" => ["mandatory" => true, "placeholder" => "__.__.__-___.__"],
            "cv" => ["type" => "file"],
            "lastActionUserId" => ["default" => User::getLoggedInUser()->id],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if (!$id && $repo->getByInsz($fields['insz'])) $this->setToast("Er bestaat al een persoon met het opgegeven rijksregisternummer!", self::VALIDATION_STATE_INVALID);

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new HRHR);
            $origItem = clone $item;
            $item->fillWithPostData($fields);
            if ($id && $fields["cv"][0]?->getSize() == 0) $item->cv = $origItem->cv;
            if (!$item->creatorUserId) $item->creatorUserId = User::getLoggedInUser()->id;

            $newId = $repo->set($item);
            if (!$id) $item->id = $newId;
            $item = $repo->getById($item->id);

            if ($fields["cv"][0] && $fields["cv"][0]->getSize() > 0) {
                $location = LOCATION_FILES . "/hr";
                FileSystem::CreateFolder($location);

                if ($fields["cv"][0]->move("{$location}/{$item->guid}." . $fields["cv"][0]->getExtension())) {
                    $item->cv = "{$item->guid}." . $fields["cv"][0]->getExtension();
                    $repo->set($item);
                }
            }

            $history = new HRHistory;
            $history->hrId = $item->id;
            $history->creatorUserId = User::getLoggedInUser()->id;
            $history->action = $id ? "UPDATE" : "CREATE";
            if ($id) {
                $updateData = [];
                foreach ($item->toSqlArray() as $key => $newValue) {
                    if ($origItem->$key !== $newValue) $updateData[$key] = $origItem->$key;
                }
                $history->data = $updateData;
            }
            $historyRepo->set($history);

            $this->executeFlow($item, $id ? "update" : "create", $origItem);
            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postRole($view, $id = null)
    {
        if ($id == "add") $id = null;
        $repo = new Role;

        $_fields = [
            "schoolId" => ["mandatory" => true],
            "name" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $item = $repo->getById($id) ?? (new HRRole);
            $item->fillWithPostData($fields);
            $repo->set($item);

            $this->setReturn();
        } else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postFlow($view, $id = null)
    {
        $settings = Helpers::input()->all();
        foreach ($settings as $k => $v) if (Strings::startsWith($k, "flow_new_roleId_") || Strings::startsWith($k, "flow_new_to_")) unset($settings[$k]);
        $this->postSettings($settings);
    }

    // Delete functions 
    protected function deleteOverview($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new HR;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De persoon '{$item->formatted->fullName}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }
    
    protected function deleteRole($view, $id = null)
    {
        $id = explode("_", $id);
        $repo = new Role;

        foreach ($id as $_id) {
            $item = Arrays::first($repo->get($_id));

            $item->deleted = 1;
            $repo->set($item);

            $this->setToast("De functie '{$item->name}' is verwijderd!");
        }

        $this->setReloadTable();
        $this->setCloseModal();
    }

    // Other functions
    private function executeFlow(HRHR $hr, $action = "create", $origItem = null)
    {
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $hr->reinit();
        $settings = $this->getSettings(true);

        $subject = Arrays::getValue($settings, (Strings::equal($action, "create") ? "flow.new.subject" : "flow.edit.subject"));
        $body = Arrays::getValue($settings, (Strings::equal($action, "create") ? "flow.new.body" : "flow.edit.body"));

        foreach ($hr->toArray(true) as $k => $v) {
            $subject = str_replace("{{{$k}}}", $v, $subject);
            $body = str_replace("{{{$k}}}", $v, $body);
        }

        $mail = new ObjectMail;
        $mail->subject = $subject;
        $mail->body = $body;

        $mailId = $mailRepo->set($mail);
        $mail->id = $mailId;
        $mail = $mailRepo->getById($mail->id);

        foreach (explode(",", Arrays::getValue($settings, (Strings::equal($action, "create") ? "flow.new.to" : "flow.edit.to"))) as $email) {
            $receiver = new MailReceiver;
            $receiver->mailId = $mailId;
            $receiver->email = trim($email);
            $mailReceiverRepo->set($receiver);
        }

        $filtered = json_decode(Arrays::getValue($settings, (Strings::equal($action, "create") ? "flow.new.filtered" : "flow.edit.filtered")), true);
        foreach ($filtered as $filter) {
            $pass = false;
            if (Strings::equal($action, "create")) {
                foreach (explode(";", $filter['roleId']) as $roleId) {
                    if (Arrays::contains(explode(";", $hr->roleId), $roleId)) $pass = true;
                }
            } else {
                $hr->formatted->listOfLast = "
                <table style='width: 100%; border-collapse: collapse; border: 1px solid #ccc; text-align: left;'>
                    <thead>
                        <tr>
                            <th width='30%'>Parameter</th>
                            <th width='35%'>Oude waarde</th>
                            <th width='35%'>Nieuwe waarde</th>
                        </tr>
                    </thead>
                    <tbody>";

                $parameters = explode(";", $filter['parameterId']);
                foreach ($hr->toSqlArray() as $key => $newValue) {
                    if ($origItem->$key !== $newValue) {
                        $hr->formatted->listOfLast .= "
                        <tr>
                            <td><b>" . HRHR::PARAMETERS[$key] . "</b></td>
                            <td>" . (($origItem->$key instanceof stdClass ? $origItem->$key->display : $origItem->formatted->$key) ?: $origItem->$key) . "</td>
                            <td>" . (($hr->formatted->$key instanceof stdClass ? $hr->formatted->$key->display : $hr->formatted->$key) ?: $hr->$key) . "</td>
                        </tr>";

                        if (Arrays::contains($parameters, $key)) $pass = true;
                    }
                }

                $hr->formatted->listOfLast .= "
                    </tbody>
                </table>";

                $body = str_replace("{{formatted.listOfLast}}", $hr->formatted->listOfLast, $body);
                $mail->body = $body;
                $mailRepo->set($mail);
            }

            if ($pass) {
                foreach (explode(",", $filter['to']) as $email) {
                    $receiver = new MailReceiver;
                    $receiver->mailId = $mailId;
                    $receiver->email = trim($email);
                    $mailReceiverRepo->set($receiver);
                }
            }
        }
    }
}
