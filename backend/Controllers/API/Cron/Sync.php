<?php

namespace Controllers\API\Cron;

use Security\User;
use Security\Input;
use Helpers\General;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\School\School;
use Database\Repository\Mail\Mail;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Mail\Receiver;
use Database\Object\Sync\Sync as ObjectSync;
use Database\Repository\School\Institute;
use Database\Repository\Informat\Student;
use Database\Object\Mail\Mail as MailMail;
use Database\Repository\Informat\Employee;
use M365\Repository\User as RepositoryUser;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\EmployeeEmail;
use Database\Repository\Sync\Sync as RepositorySync;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Navigation\Setting;
use Helpers\Log;
use Security\FileSystem;

abstract class Sync
{
    static public function Prepare()
    {
        define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
        define("_LOGLOCATION_", "cron/sync");
        Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Schoolyear: " . General::getSchoolyear());

        $prepareEmployee = self::PrepareEmployee();
        $prepareStudent = self::PrepareStudent();

        Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_);

        return ($prepareEmployee && $prepareStudent);
    }

    private static function PrepareEmployee()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to prepare employees...");
        $settingRepo = new Setting;
        $syncRepo = new RepositorySync;
        $schoolRepo = new School;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $m365UserRepo = new RepositoryUser;
        $informatEmployeeRepo = new Employee;

        $navigation = (new Navigation)->getByLink('sync');
        $_status = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.status")->value;
        $_firstName = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.createEmailWith")->value;
        $_photo = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "photo.employee")->value, 'bool');
        $_badgeId = "Badge ID";

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering all M365 employees...");
        $currentEmployees = $m365UserRepo->getAllEmployees(['id', 'employeeId', 'mail', 'accountEnabled', 'signInActivity', 'givenName', 'surname', 'displayName', 'onPremisesSamAccountName', 'onPremisesUserPrincipalName', 'companyName', 'department', 'jobTitle', 'memberOf', 'onPremisesExtensionAttributes']);
        $currentEmployees = Arrays::filter($currentEmployees, fn($ce) => Strings::equal($ce::class, \Microsoft\Graph\Generated\Models\User::class));
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Found " . count($currentEmployees) . " employees");

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering all Informat employees...");
        $informatEmployees = $informatEmployeeRepo->get();
        // $informatEmployees = Arrays::filter($informatEmployees, fn($e) => $e->informatId == 15045);
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Found " . count($informatEmployees) . " employees");
        // Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);

        foreach ($informatEmployees as $informatEmployee) {
            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$informatEmployee->informatId} - {$informatEmployee->name} {$informatEmployee->firstName}");
            $sync = $syncRepo->getByEmployeeId($informatEmployee->informatId) ?? new ObjectSync;
            $sync->type = "E";
            $sync->employeeId = $informatEmployee->informatId;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->password = null;
            $sync->thumbnailPhoto = null;
            $sync->ou = null;

            $m365User = Arrays::firstOrNull(Arrays::filter($currentEmployees, fn($cs) => Strings::equal($cs->getEmployeeId(), $informatEmployee->informatId) || Strings::equal($cs->getEmployeeId(), "P{$informatEmployee->informatId}")));
            $inService = Strings::equal(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_status))->value, "IN DIENST");

            $GivenName = (Strings::equalsIgnoreCase(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_firstName)->value ?: "Voornaam"), "voornaam") ? $informatEmployee->firstName : $informatEmployee->extraFirstName);
            $DisplayName = Input::createDisplayName($settingRepo->getByNavigationIdAndKey($navigation->id, "format.displayName")->value, $GivenName, $informatEmployee->name);
            $EmailAddress = Input::createEmail($settingRepo->getByNavigationIdAndKey($navigation->id, "format.email")->value, $GivenName, $informatEmployee->name, EMAIL_SUFFIX);
            $BadgeId = $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_badgeId)->value;
            $MemberOf = [];
            $OtherAttributes = [];

            $functions = $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdAndSection($informatEmployee->id, 2);
            $functions = Arrays::filter($functions, fn($ff) => Strings::contains($ff->name, " - Functie "));

            $schools = Arrays::map($functions, fn($f) => $f->name);
            $schools = Arrays::map($schools, fn($d) => Arrays::first(explode(" - ", $d)));
            $schools = array_unique(array_values($schools));

            if ($m365User) {
                $memberOfM365 = $m365UserRepo->getMemberOf($m365User->getId(), ['onPremisesDomainName', 'displayName']);
                $memberOfM365 = Arrays::filter($memberOfM365, fn($m) => $m->getOnPremisesDomainName() !== null);
                $memberOfM365 = Arrays::map($memberOfM365, fn($m) => $m->getDisplayName());

                $m365OU = explode(",", $m365User?->getOnPremisesDistinguishedName());
                array_shift($m365OU);
                $m365OU = implode(",", $m365OU);
            }

            foreach (explode(PHP_EOL, $settingRepo->getByNavigationIdAndKey($navigation->id, "default.memberOf.employee")->value) as $mof) {
                $mof = Strings::trimToNull($mof);
                foreach ($schools as $school) {
                    $school = $schoolRepo->getByName($school);
                    if (Strings::isBlank($school->adSecGroupPart)) continue;
                    $_mof = str_replace(["{{school:adSecGroupPart}}", "{{school:adOuPartUpper}}"], [$school->adSecGroupPart, strtoupper($school->adOuPart)], $mof);

                    if ($m365User) {
                        if (!Arrays::contains($memberOfM365, $_mof)) $MemberOf[] = $_mof;
                    } else $MemberOf[] = $_mof;
                }
            }

            $MemberOf = array_unique($MemberOf);

            $departments = Arrays::map($schools, fn($d) => $schoolRepo->getByName($d)->name);
            $departments = implode(", ", $departments);
            $departments = Strings::trimToNull($departments);

            $jobtitles = Arrays::filter($functions, fn($f) => !Strings::startsWith(Arrays::last(explode(' (', $f->value)), "!"));
            $jobtitles = Arrays::map($jobtitles, fn($f) => Arrays::first(explode(' (', $f->value)));
            $jobtitles = array_unique(array_values($jobtitles));
            $jobtitles = implode(", ", $jobtitles);
            $jobtitles = Strings::trimToNull($jobtitles);

            $_functions = $_functionsPerSchool = [];
            foreach ($functions as $function) {
                $school = $schoolRepo->getByName(Arrays::first(explode(" - ", $function->name)));
                $functionName = Arrays::first(explode("(", $function->value));
                $functionCode = str_replace(")", "", Arrays::last(explode("(", $function->value)));

                if (!Arrays::keyExists($_functions, $school->adJobTitlePrefix)) $_functions[$school->adJobTitlePrefix] = [];
                if (!Arrays::keyExists($_functionsPerSchool, $school->name)) $_functionsPerSchool[$school->name] = [];
                $_functions[$school->adJobTitlePrefix][] = $functionCode;
                $_functionsPerSchool[$school->name][] = $functionName;
            }

            if (!empty($_functions)) {
                $ea1 = [];

                foreach ($_functions as $_school => $_codes) {
                    foreach ($_codes as $_code) {
                        $_code = str_replace("!", "", $_code);
                        $ea1[] = "{$_school}:{$_code}";
                    }
                }

                $OtherAttributes["extensionAttribute1"] = Strings::trimToNull(implode(" ", $ea1));
                if (Strings::equal($OtherAttributes["extensionAttribute1"], $m365User?->getOnPremisesExtensionAttributes()->getExtensionAttribute1()) || Strings::isBlank($OtherAttributes['extensionAttribute1'])) unset($OtherAttributes["extensionAttribute1"]);
            }

            if ($BadgeId) {
                if (!Strings::equal($BadgeId, $m365User?->getOnPremisesExtensionAttributes()->getExtensionAttribute15())) {
                    $OtherAttributes["extensionAttribute15"] = $BadgeId;
                    $OtherAttributes["pager"] = $BadgeId;
                }
            }

            if ($_photo && FileSystem::PathExists(LOCATION_IMAGE . "/informat/employee/{$informatEmployee->informatGuid}.jpg")) {
                if ((time() - filemtime(LOCATION_IMAGE . "/informat/employee/{$informatEmployee->informatGuid}.jpg")) < 1200)
                    $sync->thumbnailPhoto = FileSystem::GetDownloadLink(LOCATION_IMAGE . "/informat/employee/{$informatEmployee->informatGuid}.jpg");
            }

            $CompanyName = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.companyName.employee")->value;
            if (Strings::contains($m365User?->getCompanyName(), "COLTD")) $CompanyName = "COLTD, {$CompanyName}";

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "M365: " . ($m365User ? "" : "not") . " found; M365 Enabled: " . ($m365User ? ($m365User->getAccountEnabled() ? "YES" : "NO") : "N/A") . "; Informat Active: " . ($informatEmployee->active ? "YES" : "NO") . "; In Service: " . ($inService ? "YES" : "NO"));
            // Not in M365 - Active - Create
            if (
                !$m365User &&
                $informatEmployee->active &&
                $inService
            ) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Create employee");
                $postfix = 2;
                $Email = explode("@", $EmailAddress);

                while (Arrays::filter($currentEmployees, fn($ce) => Strings::equalsIgnoreCase($ce->getMail(), $EmailAddress))) {
                    $_Email = $Email[0] . $postfix;
                    $EmailAddress = "{$_Email}@{$Email[1]}";
                    $postfix++;
                }
                $SamAccountName = substr(Arrays::first(explode("@", $EmailAddress)), 0, 20);

                $sync->action = "C";
                $sync->givenName = $GivenName;
                $sync->surname = $informatEmployee->name;
                $sync->displayName = $DisplayName;
                $sync->emailAddress = $EmailAddress;
                $sync->samAccountName = $SamAccountName;
                $sync->userPrincipalName = $EmailAddress;
                $sync->companyName = $CompanyName;
                $sync->department = $departments;
                $sync->jobTitle = $jobtitles;
                $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                $sync->otherAttributes = (is_null($OtherAttributes) || empty($OtherAttributes)) ? null : $OtherAttributes;
                $sync->password = User::generatePassword();
                $sync->ou = $settingRepo->getByNavigationIdAndKey($navigation->id, "default.ou.employee")->value;

                // foreach ($sync as $k => $v) if (!is_null($v)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$k}: {$v}");
            }
            // Disabled in M365 - Active - Enable
            else if (
                $m365User &&
                $m365User->getAccountEnabled() == false &&
                $informatEmployee->active &&
                $inService
            ) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Enable employee");

                $sync->action = "E";
                $sync->givenName = Strings::equal($GivenName, $m365User->getGivenName()) ? null : $GivenName;
                $sync->surname = Strings::equal($informatEmployee->name, $m365User->getSurname()) ? null : $informatEmployee->name;
                $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                $sync->companyName = $CompanyName;
                $sync->department = $departments;
                $sync->jobTitle = $jobtitles;
                $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                $sync->otherAttributes = (is_null($OtherAttributes) || empty($OtherAttributes)) ? null : $OtherAttributes;
                $sync->password = User::generatePassword();

                // foreach ($sync as $k => $v) if (!is_null($v)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$k}: {$v}");
            }
            // Enabled in M365 - Active - Update
            else if (
                $m365User &&
                $m365User->getAccountEnabled() == true  &&
                $informatEmployee->active &&
                $inService
            ) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Update employee");

                $sync->action = "U";
                $sync->givenName = Strings::equal($GivenName, $m365User->getGivenName()) ? null : $GivenName;
                $sync->surname = Strings::equal($informatEmployee->name, $m365User->getSurname()) ? null : $informatEmployee->name;
                $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                $sync->department = Strings::equal($departments, $m365User->getDepartment()) ? null : ($departments ?: null);
                $sync->jobTitle = Strings::equal($jobtitles, $m365User->getJobTitle()) ? null : ($jobtitles ?: null);
                $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                $sync->otherAttributes = (is_null($OtherAttributes) || empty($OtherAttributes)) ? null : $OtherAttributes;
            }
            // Enabled in M365 - Inactive - Disable
            else if (
                $m365User &&
                $m365User->getAccountEnabled() == true  &&
                (
                    !$informatEmployee->active ||
                    !$inService
                )
            ) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Disable employee");

                $sync->action = "D";
                $sync->givenName = null;
                $sync->surname = null;
                $sync->displayName = null;
                $sync->companyName = null;
                $sync->department = null;
                $sync->memberOf = null;
                $sync->thumbnailPhoto = null;
                $sync->ou = null;
            } else {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Nothing to do, skipping...");
                $sync->action = null;
            }

            if (!Strings::equal($sync->action, "D") && $sync->noUpdate()) {
                $sync->action = null;
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Nothing to do, skipping...");
            } else if (!is_null($sync->action)) {
                foreach ($sync->toSqlArray() as $k => $v) if (!is_null($v)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$k}: {$v}");
            }

            $sync->setEmail = $sync->emailAddress ?: $m365User?->getMail();
            $sync->setPassword = $sync->password ?: $sync->setPassword;

            $nId = $syncRepo->set($sync);
            if (!$sync->id) $sync = Arrays::firstOrNull($syncRepo->get($nId));

            if (Arrays::contains(["C", "E"], $sync->action)) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Preparing e-mails for new or re-enabled employee");
                self::createNewEmployeeMail($sync);
                self::createNewEmployeeToCentralMail($sync, $_functionsPerSchool);
            } else if (Arrays::contains(["D"], $sync->action)) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Preparing e-mails for disabled employee");
                self::createDisableEmployeeMail($sync);
                self::createDisableEmployeeToCentralMail($sync);
            }
        }

        return true;
    }

    private static function PrepareStudent()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting to prepare students...");
        $settingRepo = new Setting;
        $syncRepo = new RepositorySync;
        $schoolRepo = new School;
        $instituteRepo = new Institute;
        $m365UserRepo = new RepositoryUser;
        $informatStudentRepo = new Student;
        $informatRegistrationRepo = new Registration;
        $informatRegistrationClassRepo = new RegistrationClass;
        $informatClassgroupRepo = new ClassGroup;

        $navigation = (new Navigation)->getByLink('sync');
        $_minDepartmentCodes = explode(PHP_EOL, $settingRepo->getByNavigationIdAndKey($navigation->id, "minimum.departmentCode")->value);
        $_minDepartmentCodes = Arrays::map($_minDepartmentCodes, fn($m) => Strings::trimToNull($m));
        $_minGrade = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "minimum.grade")->value, 'int');
        $_minYear = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "minimum.year")->value, 'int');
        $_photo = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "photo.student")->value, 'bool');

        $create = $update = $enable = $disable = [];

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering all M365 students...");
        $currentStudents = $m365UserRepo->getAllStudents(['id', 'employeeId', 'mail', 'accountEnabled', 'signInActivity', 'givenName', 'surname', 'displayName', 'onPremisesSamAccountName', 'onPremisesUserPrincipalName', 'companyName', 'department', 'jobTitle', 'memberOf', 'onPremisesExtensionAttributes', 'onPremisesDistinguishedName']);
        $currentStudents = Arrays::filter($currentStudents, fn($ce) => Strings::equal($ce::class, \Microsoft\Graph\Generated\Models\User::class));
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Found " . count($currentStudents) . " students");

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering all Informat students...");
        $informatStudents = $informatStudentRepo->get();
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Found " . count($informatStudents) . " students");

        foreach ($informatStudents as $informatStudent) {
            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Student: {$informatStudent->informatId} - {$informatStudent->name} {$informatStudent->firstName}");

            $sync = $syncRepo->getByEmployeeId($informatStudent->informatId) ?? new ObjectSync;
            $sync->type = "S";
            $sync->employeeId = $informatStudent->informatId;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->jobTitle = null;
            $sync->otherAttributes = null;
            $sync->password = $sync->password ? $sync->password : null;
            $sync->thumbnailPhoto = null;
            $sync->ou = null;

            $m365User = Arrays::firstOrNull(Arrays::filter($currentStudents, fn($cs) => Strings::equal($cs->getEmployeeId(), $informatStudent->informatId) || Strings::equal($cs->getEmployeeId(), "L{$informatStudent->informatId}")));
            if (Strings::contains($m365User?->getOnPremisesDistinguishedName(), "OU=COLTD,OU=UsersCOLTD,OU=COLTD,DC=coltd,DC=be")) continue;

            $currentRegistration = $informatRegistrationRepo->getByInformatStudentId($informatStudent->id);
            $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => $cr->current);
            $currentRegistration = Arrays::filter($currentRegistration, fn($cr) => $cr->status == 0);
            $currentRegistration = Arrays::orderBy($currentRegistration, "start");

            if (count($currentRegistration)) $currentRegistration = Arrays::last($currentRegistration);

            $currentRegistrationClass = $informatRegistrationClassRepo->getByInformatRegistrationId($currentRegistration->id);
            $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => $crc->current);
            $currentRegistrationClass = Arrays::filter($currentRegistrationClass, function ($crc) use ($informatClassgroupRepo) {
                $class = Arrays::firstOrNull($informatClassgroupRepo->get($crc->informatClassGroupId));
                if (!$class) return false;
                if ($class->type == "C") return true;
                return false;
            });
            $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);

            $institute = $currentRegistration ? Arrays::firstOrNull($instituteRepo->get($currentRegistration->schoolInstituteId)) : null;
            $school = $institute ? Arrays::firstOrNull($schoolRepo->get($institute->schoolId)) : null;
            $class = Arrays::firstOrNull($informatClassgroupRepo->get($currentRegistrationClass->informatClassGroupId));

            $DisplayName = Input::createDisplayName($settingRepo->getByNavigationIdAndKey($navigation->id, "format.displayName")->value, $informatStudent->firstName, $informatStudent->name);
            $EmailAddress = Input::createEmail($settingRepo->getByNavigationIdAndKey($navigation->id, "format.email")->value, $informatStudent->firstName, $informatStudent->name, EMAIL_SUFFIX_STUDENT);

            $CompanyName = str_replace("{{school:name}}", $school->name, $settingRepo->getByNavigationIdAndKey($navigation->id, "default.companyName.student")->value);
            $OU = str_replace("{{school:adOuPart}}", $school->adOuPart, $settingRepo->getByNavigationIdAndKey($navigation->id, "default.ou.student")->value);

            $MemberOf = [];

            if ($m365User) {
                $memberOfM365 = $m365UserRepo->getMemberOf($m365User->getId(), ['onPremisesDomainName', 'displayName']);
                $memberOfM365 = Arrays::filter($memberOfM365, fn($m) => $m->getOnPremisesDomainName() !== null);
                $memberOfM365 = Arrays::map($memberOfM365, fn($m) => $m->getDisplayName());

                $m365OU = explode(",", $m365User?->getOnPremisesDistinguishedName());
                array_shift($m365OU);
                $m365OU = implode(",", $m365OU);
            }

            foreach (explode(PHP_EOL, $settingRepo->getByNavigationIdAndKey($navigation->id, "default.memberOf.student")->value) as $mof) {
                $mof = Strings::trimToNull($mof);
                if (is_null($school->adSecGroupPart)) continue;
                $mof = str_replace(["{{school:adSecGroupPart}}", "{{school:adOuPartUpper}}"], [$school->adSecGroupPart, strtoupper($school->adOuPart)], $mof);

                if ($m365User) {
                    if (!Arrays::contains($memberOfM365, $mof)) $MemberOf[] = $mof;
                } else $MemberOf[] = $mof;
            }
            $MemberOf = array_unique($MemberOf);

            if ($_photo && FileSystem::PathExists(LOCATION_IMAGE . "/informat/student/{$informatStudent->informatGuid}.jpg")) {
                if ((time() - filemtime(LOCATION_IMAGE . "/informat/student/{$informatStudent->informatGuid}.jpg")) < 1200)
                    $sync->thumbnailPhoto = FileSystem::GetDownloadLink(LOCATION_IMAGE . "/informat/student/{$informatStudent->informatGuid}.jpg");
            }

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "M365: " . ($m365User ? "" : "not") . " found; M365 Enabled: " . ($m365User ? ($m365User->getAccountEnabled() ? "YES" : "NO") : "N/A"));

            if (
                $currentRegistration &&
                Arrays::contains($_minDepartmentCodes, $currentRegistration->departmentCode) &&
                $currentRegistration->grade >= $_minGrade &&
                $currentRegistration->year >= $_minYear &&
                (is_null($currentRegistration->end) || Clock::now()->isBeforeOrEqualTo(Clock::at($currentRegistration->end)))
            ) {
                // Not in M365 - Registered - Create
                if (!$m365User) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Create student");

                    $postfix = 2;
                    $Email = explode("@", $EmailAddress);

                    while (Arrays::filter($currentStudents, fn($cs) => Strings::equalsIgnoreCase($cs->getMail(), $EmailAddress))) {
                        $_Email = $Email[0] . $postfix;
                        $EmailAddress = "{$_Email}@{$Email[1]}";
                        $postfix++;
                    }
                    $SamAccountName = substr(Arrays::first(explode("@", $EmailAddress)), 0, 20);

                    $sync->action = "C";
                    $sync->givenName = $informatStudent->firstName;
                    $sync->surname = $informatStudent->name;
                    $sync->displayName = $DisplayName;
                    $sync->emailAddress = $EmailAddress;
                    $sync->samAccountName = $SamAccountName;
                    $sync->userPrincipalName = $EmailAddress;
                    $sync->companyName = $CompanyName;
                    $sync->department = $class->code;
                    $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                    $sync->password = User::generatePassword();
                    $sync->ou = trim($OU);
                }
                // Disabled in M365 - Registered - Enable
                else if (
                    $m365User &&
                    $m365User->getAccountEnabled() == false
                ) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Enable student");

                    $sync->action = "E";
                    $sync->givenName = Strings::equal($informatStudent->firstName, $m365User->getGivenName()) ? null : $informatStudent->firstName;
                    $sync->surname = Strings::equal($informatStudent->name, $m365User->getSurname()) ? null : $informatStudent->name;
                    $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                    $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                    $sync->department = Strings::equal($class->code, $m365User->getDepartment()) ? null : ($class->code ?: null);
                    $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                    $sync->password = User::generatePassword();
                    $sync->ou = Strings::equal($OU, $m365OU) ? null : $OU;
                }
                // Enabled in M365 - Registered - Update
                else {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Update student");

                    $sync->action = "U";
                    $sync->givenName = Strings::equal($informatStudent->firstName, $m365User->getGivenName()) ? null : $informatStudent->firstName;
                    $sync->surname = Strings::equal($informatStudent->name, $m365User->getSurname()) ? null : $informatStudent->name;
                    $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                    $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                    $sync->department = Strings::equal($class->code, $m365User->getDepartment()) ? null : ($class->code ?: null);
                    $sync->ou = Strings::equal($OU, $m365OU) ? null : $OU;
                    $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                }
            } else {
                // Enabled in M365 - Unregistered/No account needed - Disable
                // if ($m365User && $m365User->getAccountEnabled() == true && !is_null($currentRegistration->end) && Clock::now()->isAfterOrEqualTo(Clock::at($currentRegistration->end))) {
                if (
                    $m365User &&
                    $m365User->getAccountEnabled() == true
                ) {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Disable student");

                    $sync->action = "D";
                    $sync->givenName = null;
                    $sync->surname = null;
                    $sync->displayName = null;
                    $sync->companyName = null;
                    $sync->department = null;
                    $sync->memberOf = null;
                    $sync->thumbnailPhoto = null;
                } else {
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Nothing to do, skipping...");
                    $sync->action = null;
                }
            }

            if (!Strings::equal($sync->action, "D") && $sync->noUpdate()) {
                $sync->action = null;
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Undoing actions");
            } else if (!is_null($sync->action)) {
                foreach ($sync->toSqlArray() as $k => $v) if (!is_null($v)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$k}: {$v}");
            }

            $sync->setEmail = $sync->emailAddress ?: $m365User?->getMail();
            $sync->setPassword = $sync->password ?: $sync->setPassword;

            $nId = $syncRepo->set($sync);
            if (!$sync->id) $sync = Arrays::firstOrNull($syncRepo->get($nId));

            if ($sync->action == "C") $create[$school->id][] = $sync;
            else if ($sync->action == "U") $update[$school->id][] = $sync;
            else if ($sync->action == "E") $enable[$school->id][] = $sync;
            else if ($sync->action == "D") $disable[$school->id][] = $sync;
        }

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Preparing e-mails for students");
        //self::createStudentMail($create, $update, $enable, $disable);

        return true;
    }

    private static function createNewEmployeeMail($sync)
    {
        $informatEmployeeRepo = new Employee;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $settingRepo = new Setting;

        $navigation = (new Navigation)->getByLink('sync');
        $_mainSchool = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.mainSchool")->value;
        $_mailType = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.mailType")->value;

        $employee = $informatEmployeeRepo->getByInformatId($sync->employeeId);

        $subject = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employee.subject")->value;
        $body = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employee.body")->value;

        $subject = str_replace("{{employee:ownfield.mainSchool}}", $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_mainSchool)->value, $subject);
        $body = str_replace("{{employee:ownfield.mainSchool}}", $informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($employee->id, 2, $_mainSchool)->value, $body);
        $body = str_replace("{{employee:formatted.fullNameReversed}}", $employee->formatted->fullNameReversed, $body);
        $body = str_replace("{{employee:functions.schools}}", $sync->department, $body);
        $body = str_replace("{{employee:functions.list}}", $sync->jobTitle, $body);
        $body = str_replace("{{sync:setEmail}}", $sync->setEmail, $body);
        $body = str_replace("{{sync:setPassword}}", $sync->setPassword, $body);

        $email = (new EmployeeEmail)->getByInformatEmployeeId($employee->id);
        $email = Arrays::filter($email, fn($e) => Strings::equal($e->type, $_mailType));

        $mail = new MailMail;
        $mail->subject = $subject;
        $mail->body = $body;

        $mId = $mailRepo->set($mail);

        foreach ($email as $_email) {
            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->name = $employee->formatted->fullName;
            $receiver->email = $_email->email;

            $mailReceiverRepo->set($receiver);
        }
    }

    private static function createDisableEmployeeMail($sync)
    {
        $informatEmployeeRepo = new Employee;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $settingRepo = new Setting;

        $navigation = (new Navigation)->getByLink('sync');
        $_mailType = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.mailType")->value;

        $employee = $informatEmployeeRepo->getByInformatId($sync->employeeId);

        $subject = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employeedisable.subject")->value;
        $body = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employeedisable.body")->value;

        $body = str_replace("{{employee:formatted.fullNameReversed}}", $employee->formatted->fullNameReversed, $body);
        $body = str_replace("{{sync:setEmail}}", $sync->setEmail, $body);

        $email = (new EmployeeEmail)->getByInformatEmployeeId($employee->id);
        $email = Arrays::filter($email, fn($e) => Strings::equal($e->type, $_mailType));

        $mail = new MailMail;
        $mail->subject = $subject;
        $mail->body = $body;

        $mId = $mailRepo->set($mail);

        foreach ($email as $_email) {
            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->name = $employee->formatted->fullName;
            $receiver->email = $_email->email;

            $mailReceiverRepo->set($receiver);
        }
    }

    private static function createNewEmployeeToCentralMail($sync, $functionsPerSchool)
    {
        $informatEmployeeRepo = new Employee;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $settingRepo = new Setting;

        $navigation = (new Navigation)->getByLink('sync');

        $employee = $informatEmployeeRepo->getByInformatId($sync->employeeId);

        $subject = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employeecentral.subject")->value;
        $body = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employeecentral.body")->value;

        $functionhtml = "";

        foreach ($functionsPerSchool as $school => $functions) {
            $functionhtml .= "
            <b>{$school}</b><br />
            <ul>";

            foreach ($functions as $f) $functionhtml .= "<li>{$f}</li>";

            $functionhtml .= "
            </ul>
            ";
        }

        $subject = str_replace("{{employee:formatted.fullNameReversed}}", $employee->formatted->fullNameReversed, $subject);
        $body = str_replace("{{sync:department}}", $sync->department, $body);
        $body = str_replace("{{sync:functionsPerSchool}}", $functionhtml, $body);
        $body = str_replace("{{employee:name}}", $employee->name, $body);
        $body = str_replace("{{employee:firstName}}", $employee->firstName, $body);
        $body = str_replace("{{sync:setEmail}}", $sync->setEmail, $body);
        $body = str_replace("{{sync:setPassword}}", $sync->setPassword, $body);

        $mail = new MailMail;
        $mail->subject = $subject;
        $mail->body = $body;

        $mId = $mailRepo->set($mail);

        $emails = [
            "Nicolas Roegis" => "nicolas.roegis@coltd.be",
            "Directeur-Coördinator KaBoE" => "dirco.kaboe@coltd.be",
            "ICT-Dienst KaBoE" => "ict.kaboe@coltd.be"
        ];

        foreach ($emails as $name => $email) {
            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->name = $name;
            $receiver->email = $email;

            $mailReceiverRepo->set($receiver);
        }
    }

    private static function createDisableEmployeeToCentralMail($sync)
    {
        $informatEmployeeRepo = new Employee;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $settingRepo = new Setting;

        $navigation = (new Navigation)->getByLink('sync');

        $employee = $informatEmployeeRepo->getByInformatId($sync->employeeId);

        $subject = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employeedisablecentral.subject")->value;
        $body = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.employeedisablecentral.body")->value;

        $subject = str_replace("{{employee:formatted.fullNameReversed}}", $employee->formatted->fullNameReversed, $subject);
        $body = str_replace("{{employee:formatted.fullNameReversed}}", $employee->formatted->fullNameReversed, $body);
        $body = str_replace("{{sync:setEmail}}", $sync->setEmail, $body);

        $mail = new MailMail;
        $mail->subject = $subject;
        $mail->body = $body;

        $mId = $mailRepo->set($mail);

        $emails = [
            "Nicolas Roegis" => "nicolas.roegis@coltd.be",
            "Directeur-Coördinator KaBoE" => "dirco.kaboe@coltd.be",
            "ICT-Dienst KaBoE" => "ict.kaboe@coltd.be"
        ];

        foreach ($emails as $name => $email) {
            $receiver = new MailReceiver;
            $receiver->mailId = $mId;
            $receiver->name = $name;
            $receiver->email = $email;

            $mailReceiverRepo->set($receiver);
        }
    }

    private static function createStudentMail($create = [], $update = [], $enable = [], $disable = [])
    {
        $navRepo = new Navigation;
        $schoolRepo = new School;
        $informatStudentRepo = new Student;
        $informatRegistrationRepo = new Registration;
        $informatRegistrationClassRepo = new RegistrationClass;
        $informatClassgroupRepo = new ClassGroup;
        $settingRepo = new Setting;

        $navigation = (new Navigation)->getByLink('sync');

        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        foreach ($schoolRepo->get() as $school) {
            if (!Arrays::keyExists($create, $school->id) && !Arrays::keyExists($update, $school->id) && !Arrays::keyExists($enable, $school->id) && !Arrays::keyExists($disable, $school->id)) continue;

            $subject = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.student.subject")->value;
            $body = $settingRepo->getByNavigationIdAndKey($navigation->id, "mail.template.student.body")->value;

            foreach ($school->toArray(true) as $k => $v) {
                $subject = str_replace("{{school:{$k}}}", $v, $subject);
                $body = str_replace("{{school:{$k}}}", $v, $body);
            }

            $body = str_replace("{{sync:count.create}}", count($create[$school->id] ?: []) ?: 0, $body);
            $body = str_replace("{{sync:count.update}}", count($update[$school->id] ?: []) ?: 0, $body);
            $body = str_replace("{{sync:count.enable}}", count($enable[$school->id] ?: []) ?: 0, $body);
            $body = str_replace("{{sync:count.disable}}", count($disable[$school->id] ?: []) ?: 0, $body);

            $tblCreate = $tblUpdate = $tblEnable = $tblDisable = "";

            if (count($create[$school->id] ?: [])) {
                $tblCreate = "
                <table style='border-collapse: collapse; width: 100%'>
                    <thead>
                        <tr>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Klas</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Naam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Voornaam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>E-Mail</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Wachtwoord</th>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($create[$school->id] as $sync) {
                    $informatStudent = $informatStudentRepo->getByInformatId($sync->employeeId);
                    $lastRegistration = $informatRegistrationRepo->getByInformatStudentId($informatStudent->id);
                    $lastRegistration = Arrays::orderBy($lastRegistration, "start");
                    $lastRegistration = Arrays::filter($lastRegistration, fn($lr) => $lr->status == 0);

                    if (count($lastRegistration) == 1) $lastRegistration = Arrays::last($lastRegistration);

                    $currentRegistrationClass = $informatRegistrationClassRepo->getByInformatRegistrationId($lastRegistration->id);
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => Clock::now()->isAfterOrEqualTo(Clock::at($crc->start)));
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => is_null($crc->end));
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, function ($crc) use ($informatClassgroupRepo) {
                        $class = Arrays::firstOrNull($informatClassgroupRepo->get($crc->informatClassGroupId));
                        if (!$class) return false;
                        if ($class->type == "C") return true;
                        return false;
                    });
                    $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);
                    $class = Arrays::firstOrNull($informatClassgroupRepo->get($currentRegistrationClass->informatClassGroupId));

                    $tblCreate .= "
                        <tr>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$class->code}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->name}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->firstName}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$sync->setEmail}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$sync->setPassword}</td>
                        </tr>
                    ";
                }

                $tblCreate .= "
                    </tbody>
                </table>
                ";
            }

            if (count($update[$school->id] ?: [])) {
                $tblUpdate = "
                <table style='border-collapse: collapse; width: 100%'>
                    <thead>
                        <tr>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Klas</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Naam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Voornaam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>E-Mail</th>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($update[$school->id] as $sync) {
                    $informatStudent = $informatStudentRepo->getByInformatId($sync->employeeId);
                    $lastRegistration = $informatRegistrationRepo->getByInformatStudentId($informatStudent->id);
                    $lastRegistration = Arrays::orderBy($lastRegistration, "start");
                    $lastRegistration = Arrays::filter($lastRegistration, fn($lr) => $lr->status == 0);

                    if (count($lastRegistration) == 1) $lastRegistration = Arrays::last($lastRegistration);

                    $currentRegistrationClass = $informatRegistrationClassRepo->getByInformatRegistrationId($lastRegistration->id);
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => Clock::now()->isAfterOrEqualTo(Clock::at($crc->start)));
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => is_null($crc->end));
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, function ($crc) use ($informatClassgroupRepo) {
                        $class = Arrays::firstOrNull($informatClassgroupRepo->get($crc->informatClassGroupId));
                        if (!$class) return false;
                        if ($class->type == "C") return true;
                        return false;
                    });
                    $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);
                    $class = Arrays::firstOrNull($informatClassgroupRepo->get($currentRegistrationClass->informatClassGroupId));

                    $tblUpdate .= "
                        <tr>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$class->code}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->name}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->firstName}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$sync->setEmail}</td>
                        </tr>
                    ";
                }

                $tblUpdate .= "
                    </tbody>
                </table>
                ";
            }

            if (count($enable[$school->id] ?: [])) {
                $tblEnable = "
                <table style='border-collapse: collapse; width: 100%'>
                    <thead>
                        <tr>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Klas</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Naam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Voornaam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>E-Mail</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Wachtwoord</th>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($enable[$school->id] as $sync) {
                    $informatStudent = $informatStudentRepo->getByInformatId($sync->employeeId);
                    $lastRegistration = $informatRegistrationRepo->getByInformatStudentId($informatStudent->id);
                    $lastRegistration = Arrays::orderBy($lastRegistration, "start");
                    $lastRegistration = Arrays::filter($lastRegistration, fn($lr) => $lr->status == 0);

                    if (count($lastRegistration) == 1) $lastRegistration = Arrays::last($lastRegistration);

                    $currentRegistrationClass = $informatRegistrationClassRepo->getByInformatRegistrationId($lastRegistration->id);
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => Clock::now()->isAfterOrEqualTo(Clock::at($crc->start)));
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, fn($crc) => is_null($crc->end));
                    $currentRegistrationClass = Arrays::filter($currentRegistrationClass, function ($crc) use ($informatClassgroupRepo) {
                        $class = Arrays::firstOrNull($informatClassgroupRepo->get($crc->informatClassGroupId));
                        if (!$class) return false;
                        if ($class->type == "C") return true;
                        return false;
                    });
                    $currentRegistrationClass = Arrays::firstOrNull($currentRegistrationClass);
                    $class = Arrays::firstOrNull($informatClassgroupRepo->get($currentRegistrationClass->informatClassGroupId));

                    $tblEnable .= "
                        <tr>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$class->code}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->name}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->firstName}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$sync->setEmail}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$sync->setPassword}</td>
                        </tr>
                    ";
                }

                $tblEnable .= "
                    </tbody>
                </table>
                ";
            }

            if (count($disable[$school->id] ?: [])) {
                $tblDisable = "
                <table style='border-collapse: collapse; width: 100%'>
                    <thead>
                        <tr>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Naam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>Voornaam</th>
                            <th style='border: 1px solid #dddddd; text-align: left; padding: 8px'>E-Mail</th>
                        </tr>
                    </thead>
                    <tbody>
                ";

                foreach ($disable[$school->id] as $sync) {
                    $informatStudent = $informatStudentRepo->getByInformatId($sync->employeeId);

                    $tblDisable .= "
                        <tr>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->name}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$informatStudent->firstName}</td>
                            <td style='border: 1px solid #dddddd; text-align: left; padding: 8px'>{$sync->setEmail}</td>
                        </tr>
                    ";
                }

                $tblDisable .= "
                    </tbody>
                </table>
                ";
            }

            $body = str_replace("{{sync:table.create}}", $tblCreate, $body);
            $body = str_replace("{{sync:table.update}}", $tblUpdate, $body);
            $body = str_replace("{{sync:table.enable}}", $tblEnable, $body);
            $body = str_replace("{{sync:table.disable}}", $tblDisable, $body);

            $mail = new MailMail;
            $mail->subject = $subject;
            $mail->body = $body;

            $mId = $mailRepo->set($mail);

            foreach ($school->syncUpdateMail as $r) {
                $receiver = new MailReceiver;
                $receiver->mailId = $mId;
                $receiver->email = $r;
                $mailReceiverRepo->set($receiver);
            }
        }
    }
}
