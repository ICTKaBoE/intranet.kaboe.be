<?php

namespace Controllers\API;

use CloudMersive\Convert;
use Controllers\ApiController;
use Database\Object\Mail\Mail as ObjectMail;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Object\Student\Document as StudentDocument;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\ClassGroupTeacher;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\StudentAddress;
use Database\Repository\Informat\StudentBank;
use Database\Repository\Informat\StudentConfig;
use Database\Repository\Informat\StudentEmail;
use Database\Repository\Informat\StudentNumber;
use Database\Repository\Informat\StudentRelation;
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Database\Repository\Navigation\Setting;
use Database\Repository\School\Institute;
use Database\Repository\School\School;
use Database\Repository\Smartschool\User as SmartschoolUser;
use Database\Repository\Smartschool\UserCoAccount;
use Database\Repository\Source;
use Database\Repository\Student\Document;
use DocxMerge\DocxMerge;
use Helpers\Filter;
use Helpers\Form;
use Helpers\General;
use Helpers\Log;
use Helpers\Table;
use HeroQR\Core\QRCodeGenerator;
use M365\Repository\User as RepositoryUser;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use PhpOffice\PhpWord\TemplateProcessor;
use Router\Helpers;
use Security\FileSystem;
use Security\GUID;
use Security\Input;
use Security\User;
use Smartschool\Smartschool;

class StudentController extends ApiController
{
    const CURRENT_NAVIGATION_MODULE_NAME = "student";

    // GET
    protected function getOverview($view, $id = null)
    {
        $configRepo = new StudentConfig;
        $regRepo = new Registration;
        $regClassRepo = new RegistrationClass;
        $classRepo = new ClassGroup;
        $schoolRepo = new School;
        $instituteRepo = new Institute;
        $smartschoolUserRepo = new SmartschoolUser;
        $smartschoolUserCoRepo = new UserCoAccount;

        $addressRepo = new StudentAddress;
        $relationRepo = new StudentRelation;
        $numberRepo = new StudentNumber;
        $emailRepo = new StudentEmail;
        $bankRepo = new StudentBank;

        $filters = Filter::Find(['schoolyearId', 'schoolId']);
        if (empty($filters['schoolyearId'])) $filters['schoolyearId'] = [(new Schoolyear)->getCurrent()->id];

        if (Strings::equal($view, self::VIEW_TABLE)) {
            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $configRepo->get(filters: $filters);

            $this->appendToJson("rows", array_values($items));
        } else if (Strings::equal($view, self::VIEW_LIST) && $id) {
            $co = Helpers::url()->getParam("co");
            $what = Helpers::url()->getParam("what");

            $config = $configRepo->getById($id);
            if (!$config->linked->informatStudent) return;

            if ($what) {
                if (Strings::equal($what, "image")) {
                    $items[] = ["informatGuid" => $config->linked->informatStudent->informatGuid, "fullNameReversed" => $config->linked->informatStudent->formatted->fullNameReversed];
                }

                if (Strings::equal($what, "address")) {
                    $addresses = $addressRepo->getByInformatStudentId($config->informatStudentId);
                    $addresses = Arrays::map($addresses, fn($a) => $a->formatted->full);
                    $addresses = implode("<br />", $addresses);
                    $items[][$what] = $addresses;
                }

                if (Strings::equal($what, "relation")) {
                    $relations = $relationRepo->getByInformatStudentId($config->informatStudentId);
                    $relations = Arrays::map($relations, fn($r) => $r->formatted->typeWithFullNameReversed);
                    $relations = implode("<br />", $relations);
                    $items[][$what] = $relations;
                }

                if (Strings::equal($what, "number")) {
                    $numbers = $numberRepo->getByInformatStudentId($config->informatStudentId);
                    $numbers = Arrays::map($numbers, fn($n) => $n->formatted->typeWithLink);
                    $numbers = implode("<br />", $numbers);
                    $items[][$what] = $numbers;
                }

                if (Strings::equal($what, "email")) {
                    $emails = $emailRepo->getByInformatStudentId($config->informatStudentId);
                    $emails = Arrays::map($emails, fn($e) => $e->formatted->typeWithLink);
                    $emails = implode("<br />", $emails);
                    $items[][$what] = $emails;
                }

                if (Strings::equal($what, "bank")) {
                    $banks = $bankRepo->getByInformatStudentId($config->informatStudentId);
                    $banks = Arrays::map($banks, fn($b) => $b->formatted->details);
                    $banks = implode("<br />", $banks);
                    $items[][$what] = $banks;
                }

                if (Strings::equal($what, "history")) {
                    $registrations = $regRepo->getByInformatStudentId($config->informatStudentId);
                    $registrations = Arrays::filter($registrations, fn($cr) => $cr->status == 0);
                    $registrations = array_reverse(Arrays::orderBy($registrations, "start"));

                    $history = "";
                    foreach ($registrations as $registration) {
                        $school = $schoolRepo->getById($instituteRepo->getById($registration->schoolInstituteId)->schoolId);
                        $classRegistrations = $regClassRepo->getByInformatRegistrationId($registration->id);
                        $classRegistrations = array_reverse(Arrays::orderBy($classRegistrations, "start"));

                        $history .= "{$school->name} (Stamnummer {$registration->basenumber}) - {$registration->formatted->dates}";
                        $history .= "<ul>";

                        foreach ($classRegistrations as $cr) {
                            $classgroup = $classRepo->getById($cr->informatClassGroupId);
                            $history .= "<li>{$classgroup->code} (n°{$cr->rank}) - {$cr->formatted->dates}</li>";
                        }

                        $history .= "</ul>";
                    }
                    $items[][$what] = $history;
                }
            } else {
                $user = $smartschoolUserRepo->getByInternalId($config->linked->informatStudent->informatId);
                if (Strings::equal($co, 0)) {
                    $items = [
                        [
                            "title" => "Naam",
                            "content" => $config->linked->informatStudent->name
                        ],
                        [
                            "title" => "Voornaam",
                            "content" => $config->linked->informatStudent->firstName
                        ],
                        [
                            "title" => "Intern nummer",
                            "content" => $config->linked->informatStudent->informatId
                        ],
                        [
                            "title" => "Geboortedatum",
                            "content" => $config->linked->informatStudent->formatted->birthDate
                        ],
                        [
                            "title" => "Geboorteplaats",
                            "content" => $config->linked->informatStudent->birthPlace
                        ],
                    ];
                } else {
                    $coaccount = $smartschoolUserCoRepo->getByUserIdAndIndex($user->id, $co);
                    if ($coaccount->type == 0 || (!$coaccount->name && !$coaccount->firstName)) {
                        $items = [
                            ["title" => "Geen co-account", "content" => ""]
                        ];
                    } else {
                        $items = [
                            [
                                "title" => "Type",
                                "content" => $coaccount->mapped->type
                            ],
                            [
                                "title" => "Naam",
                                "content" => $coaccount->formatted->fullNameReversed
                            ],
                            [
                                "title" => "Status",
                                "content" => $coaccount->mapped->status
                            ],
                            [
                                "title" => "Laatste aanmelding",
                                "content" => $coaccount->lastLogin ? Clock::at($coaccount->lastLogin)->format("d/m/Y H:i:s") : "Nooit aangemeld"
                            ],
                            [
                                "title" => "E-mail",
                                "content" => $coaccount->email . " (" . ($coaccount->emailVerified ? "" : "niet") . " geverifiëerd)"
                            ],
                            [
                                "title" => "GSM/Telefoon",
                                "content" => $coaccount->mobile . " | " . $coaccount->phone
                            ]
                        ];
                    }
                }
            }

            $this->appendToJson('raw', General::processTemplate($items, searchPrePost: "#"));
        }
    }

    protected function getDocuments($view, $id = null)
    {
        $repo = new Document;

        if (Strings::equal($view, self::VIEW_TABLE)) {
            $filters = [];

            [$defaultOrder, $columns] = Table::Format();
            $this->appendToJson('defaultOrder', $defaultOrder);
            $this->appendToJson('columns', $columns);

            $items = $repo->get($id, filters: $filters);
            $this->appendToJson("rows", $items);
        } else if (Strings::equal($view, self::VIEW_SELECT)) $this->appendToJson('items', $repo->get());
        else if (Strings::equal($view, self::VIEW_FORM)) $this->appendToJson('fields', $repo->getById($id));
    }

    // POST
    protected function postOverviewResetPasswordSingle($view, $id = null)
    {
        $m365UserRepo = new RepositoryUser;
        $smartschoolUserRepo = new SmartschoolUser;
        $smartschoolUserCoRepo = new UserCoAccount;

        $settingsRepo = new Setting;
        $documentRepo = new Document;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $_fields = [
            "main" => ["convert" => "bool"],
            "mainForceChangePasswordNextLogin" => ["convert" => "bool"],
            "mainPrintDocument" => ["convert" => "bool"],
            "co1" => ["convert" => "bool"],
            "co2" => ["convert" => "bool"],
            "co3" => ["convert" => "bool"],
            "co4" => ["convert" => "bool"],
            "co5" => ["convert" => "bool"],
            "co6" => ["convert" => "bool"],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        $config = (new StudentConfig)->getById($id);
        $smartschoolSourceId = $config->linked->school->linked->parentSchool->smartschoolSourceId ?: $config->linked->school->smartschoolSourceId;
        $user = $m365UserRepo->getByEmployeeId(DEV_MODE ? "9876543210" : $config->linked->informatStudent->informatId, ['id', 'userPrincipalName']);

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", User::getLoggedInUser()->formatted->fullNameReversed . " requested password reset for {$config->linked->informatStudent->formatted->fullNameReversed}", _LOGID_);
        if ($fields['main']) {
            $newPassword = User::generatePassword(12, true);

            try {
                if (!DEV_MODE) $result = $m365UserRepo->resetPassword($user->getId(), $newPassword, $fields['mainForceChangePasswordNextLogin']);
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Main account: success", _LOGID_);

                $this->setToast("Het wachtwoord van '{$config->linked->informatStudent->formatted->fullNameReversed}' wordt gewijzigd naar '{$newPassword}'.");

                if ($fields["mainPrintDocument"]) {
                    $folder = FileSystem::CreateFolder(LOCATION_FILES . "/student/passwordReset");
                    $document = $documentRepo->getById($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "default.document.mainResetPassword")->value);

                    $saveFilename = $folder . "/" . Clock::nowAsString("YmdHis") . ".{$document->ext}";
                    $template = new TemplateProcessor(LOCATION_FILES . "/student/_default_/{$document->guid}.{$document->ext}");

                    foreach ($config->linked->school->toArray(true) as $key => $value) $template->setValue("school:{$key}", $value);
                    foreach ($config->linked->informatStudent->toArray(true) as $key => $value) $template->setValue("student:{$key}", $value);
                    foreach (User::getLoggedInUser()->toArray(true) as $key => $value) $template->setValue("user:{$key}", $value);
                    $template->setValue("student:email", $user->getUserPrincipalName());
                    $template->setValue("student:password", $newPassword);

                    foreach ($template->getVariables() as $var) $template->setValue($var, '');
                    $template->saveAs($saveFilename);

                    (new Convert)->convert($saveFilename, str_replace(".docx", ".pdf", $saveFilename));
                    FileSystem::RemoveFile($saveFilename);

                    $this->appendToJson('download', FileSystem::GetDownloadLink(str_replace(".docx", ".pdf", $saveFilename)));
                }
            } catch (\Exception $e) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "Main account: Failed ({$e->getMessage()})", _LOGID_);
            }
        }

        for ($i = 1; $i <= 6; $i++) {
            if ($fields["co{$i}"]) {
                $smartschoolUser = $smartschoolUserRepo->getByInternalId($config->linked->informatStudent->informatId);
                $coAccount = $smartschoolUserCoRepo->getByUserIdAndIndex($smartschoolUser->id, $i);

                if ($coAccount->type == 0 || (!$coAccount->name && !$coAccount->firstName)) {
                    $this->setToast("Er is geen co-account gekoppeld aan co-account {$i}.", self::VALIDATION_STATE_INVALID);
                    continue;
                }

                $newPassword = "{$coAccount->mapped->type}" . Input::clean($config->linked->informatStudent->firstName) . Clock::nowAsString("Y") . "!";
                if (!DEV_MODE) $result = Smartschool::SavePassword(DEV_MODE ? "smartschool-bo" : $smartschoolSourceId, $user->getUserPrincipalName(), $newPassword, $i);

                if (is_int($result) && $result == 0) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$smartschoolSourceId} - Co-account {$i}: success", _LOGID_);
                    $this->setToast("Het wachtwoord voor co-account {$i} van '{$config->linked->informatStudent->formatted->fullNameReversed}' wordt gewijzigd naar '{$newPassword}'.<br />Deze informatie is ook doorgestuurd naar het gekoppelde e-mailadres '{$coAccount->email}'.");

                    $mail = new ObjectMail;

                    $fromEmail = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "send.from.email")->value;
                    $fromName = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "send.from.name")->value;
                    $subject = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.coaccount.subject")->value;
                    $body = $settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "mail.template.coaccount.body")->value;

                    foreach ($coAccount->toArray(true) as $key => $value) $body = str_replace("{{coaccount:{$key}}}", $value, $body);
                    foreach ((new Source)->getById($smartschoolSourceId)->toArray(true) as $key => $value) $body = str_replace("{{smartschool:{$key}}}", $value, $body);
                    foreach ($config->linked->informatStudent->toArray(true) as $key => $value) $body = str_replace("{{account:{$key}}}", $value, $body);

                    $body = str_replace("{{newPassword}}", $newPassword, $body);
                    $body = str_replace("{{coaccount:mapped.type}}", $coAccount->mapped->type, $body);
                    $body = str_replace("{{account:username}}", $user->getUserPrincipalName(), $body);

                    $mail->fromEmail = $fromEmail;
                    $mail->fromName = $fromName;
                    $mail->subject = $subject;
                    $mail->body = $body;

                    $mId = $mailRepo->set($mail);

                    $receiver = new MailReceiver;
                    $receiver->mailId = $mId;
                    $receiver->email = DEV_MODE ? DEV_CONTACT : $coAccount->email;
                    $receiver->name = $coAccount->formatted->fullNameReversed;
                    $mailReceiverRepo->set($receiver);
                } else {
                    $this->setToast("Er is een fout opgetreden tijdens het resetten van het wachtwoord voor co-account {$i} van '{$config->linked->informatStudent->formatted->fullNameReversed}'.", self::VALIDATION_STATE_INVALID);
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$smartschoolSourceId} - Co-account {$i}: Failed", _LOGID_);
                }
            }
        }

        $this->setCloseModal('changePasswordSingle');
    }

    protected function postDocuments($view, $id = null)
    {
        if ($id == "add") $id = null;

        $_fields = [
            "alias" => ["mandatory" => true],
            "file" => ["type" => "file"]
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        if ($this->validationIsAllGood()) {
            $repo = new Document;
            $file = $fields["file"];

            $item = $repo->getById($id) ?? new StudentDocument;
            $origName = $item->name;
            $item->fillWithPostData();
            $item->guid = $item->guid ?? GUID::create();

            if ($file && $file[0]->getSize() > 0) {
                $item->name = $file[0]->getFilename();
                $item->ext = $file[0]->getExtension();
                FileSystem::CreateFolder(LOCATION_FILES . "/student/_default_/");
                $file[0]->move(LOCATION_FILES . "/student/_default_/{$item->guid}.{$item->ext}");
            } else {
                $item->name = $origName;
            }

            $repo->set($item);
        }

        if ($this->validationIsAllGood()) $this->setReturn();
        else $this->setToast("Gelieve de vereiste velden in vullen!", self::VALIDATION_STATE_INVALID);
    }

    protected function postOverviewPrint($view, $id = null)
    {
        $configRepo = new StudentConfig;
        $smsUserRepo = new SmartschoolUser;
        $settingsRepo = new Setting;
        $documentRepo = new Document;
        $sourceRepo = new Source;
        $classgroupTeacherRepo = new ClassGroupTeacher;

        $id = explode("_", $id);
        $_fields = [
            "what" => ["mandatory" => true],
        ];

        [$invalid, $fields] = Form::Validate($_fields);
        Arrays::each($invalid, fn($k) => $this->setValidation($k, Arrays::getNestedValue($_fields, [$k, "fieldError"]), self::VALIDATION_STATE_INVALID));

        $folder = LOCATION_FILES . "/student/{$fields['what']}/";
        $filename = date("YmdHis");
        $filenamePdf = $filename . ".pdf";
        $filenameDocx = $filename . ".docx";
        $qrFolder = LOCATION_IMAGE . "smartschool/qr/";

        if ($this->validationIsAllGood()) {
            FileSystem::CreateFolder($folder);
            FileSystem::CreateFolder($qrFolder);

            if (Strings::equal($fields['what'], "card")) {
                $document = $documentRepo->getById($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "default.document.card")->value);
                $merges = [];

                foreach ($id as $_id) {
                    $config = $configRepo->getById($_id);
                    (new QRCodeGenerator)->setData($smsUserRepo->getByInternalId($config->linked->informatStudent->informatId)->scannableCode)->setSize(600)->generate()->saveTo("{$qrFolder}{$config->linked->informatStudent->informatId}");

                    $smartschoolSourceId = $config->linked->school->linked->parentSchool->smartschoolSourceId ?: $config->linked->school->smartschoolSourceId;
                    $saveFilename = $folder . "/{$filename}_{$_id}.{$document->ext}";

                    $template = new TemplateProcessor(LOCATION_FILES . "/student/_default_/{$document->guid}.{$document->ext}");

                    foreach ($config->linked->informatStudent->toArray(true) as $key => $value) $template->setValue("student:{$key}", $value);
                    $template->setImageValue("smartschool:qr", "{$qrFolder}{$config->linked->informatStudent->informatId}.png");

                    foreach ($template->getVariables() as $var) $template->setValue($var, '');
                    $template->saveAs($saveFilename);
                    $merges[] = $saveFilename;
                    FileSystem::RemoveFile("{$qrFolder}{$config->linked->informatStudent->informatId}.png");
                }

                (new DocxMerge)->merge($merges, "{$folder}{$filenameDocx}", true);

                $this->setEnableForm();
                $this->appendToJson('download', FileSystem::GetDownloadLink("{$folder}{$filenameDocx}"));
                // $pdf = new PDF($fields['what'], "{$folder}{$filenamePdf}", "L", [55, 87]);
                // $pdf->SetMargins(0, 0, 0);
                // $pdf->setPrintHeader(false);
                // $pdf->setPrintFooter(false);
                // $pdf->SetAutoPageBreak(TRUE, 0);

                // foreach ($id as $_id) {
                // $config = $configRepo->getById($_id);
                // (new QRCodeGenerator)->setData($smsUserRepo->getByInternalId($config->linked->informatStudent->informatId)->scannableCode)->setSize(600)->generate()->saveTo("{$qrFolder}{$config->linked->informatStudent->informatId}");

                // $pdf->AddPage();

                // // QR
                // $pdf->Image("{$qrFolder}{$config->linked->informatStudent->informatId}.png", 50, 5, 30, 30);
                // FileSystem::RemoveFile("{$qrFolder}{$config->linked->informatStudent->informatId}.png");

                // // BirthDate
                // $pdf->setFontSize(7);
                // $pdf->Text(5, 34, Clock::at($config->linked->informatStudent->birthDate)->format("d/m/Y"));
                // $pdf->Line(6, 37.5, 11, 37.5);

                // // FirstName
                // $pdf->setFontSize(17);
                // $pdf->Text(5, 38, $config->linked->informatStudent->firstName);

                // // Name
                // $pdf->setFontSize(17);
                // $pdf->Text(5, 44, $config->linked->informatStudent->name);
                // }

                // $pdf->save();

                // $this->setEnableForm();
                // $this->appendToJson('download', FileSystem::GetDownloadLink("{$folder}{$filenamePdf}"));
            } else if (Strings::equal($fields['what'], "cover")) {
                $document = $documentRepo->getById($settingsRepo->getByNavigationIdAndKey(CURRENT_NAVIGATION_MODULE_ID, "default.document.reportCover")->value);
                $merges = [];

                foreach ($id as $_id) {
                    $config = $configRepo->getById($_id);
                    $smartschoolSourceId = $config->linked->school->linked->parentSchool->smartschoolSourceId ?: $config->linked->school->smartschoolSourceId;
                    $saveFilename = $folder . "/{$filename}_{$_id}.{$document->ext}";

                    $template = new TemplateProcessor(LOCATION_FILES . "/student/_default_/{$document->guid}.{$document->ext}");

                    foreach ($config->linked->informatStudent->toArray(true) as $key => $value) $template->setValue("student:{$key}", $value);
                    foreach ($config->linked->classgroup->toArray(true) as $key => $value) $template->setValue("class:{$key}", $value);
                    foreach ($sourceRepo->getById($smartschoolSourceId)->toArray(true) as $key => $value) $template->setValue("smartschool:{$key}", $value);
                    $template->setValue("class:rank", $config->rank);

                    $classgroupTeachers = join(", ", Arrays::map($classgroupTeacherRepo->getByInformatClassgroupId($config->classgroupId), fn($ct) => $ct->linked->informatEmployee->formatted->fullName));
                    $template->setValue("class:teachers", $classgroupTeachers);

                    foreach ($template->getVariables() as $var) $template->setValue($var, '');
                    $template->saveAs($saveFilename);
                    $merges[] = $saveFilename;
                }

                (new DocxMerge)->merge($merges, "{$folder}{$filenameDocx}", true);

                $this->setEnableForm();
                $this->appendToJson('download', FileSystem::GetDownloadLink("{$folder}{$filenameDocx}"));
            }
        }
    }
}
