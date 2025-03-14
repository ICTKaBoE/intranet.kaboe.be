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
use Database\Repository\Navigation;
use Database\Repository\Mail\Receiver;
use Database\Object\Sync as ObjectSync;
use Database\Repository\School\Institute;
use Database\Repository\Informat\Student;
use Database\Object\Mail\Mail as MailMail;
use Database\Repository\Informat\Employee;
use M365\Repository\User as RepositoryUser;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration;
use Database\Repository\Informat\EmployeeEmail;
use Database\Repository\Sync as RepositorySync;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\RegistrationClass;
use Security\FileSystem;

abstract class Sync
{
    static public function Prepare()
    {
        $prepareEmployee = self::PrepareEmployee();
        $prepareStudent = self::PrepareStudent();

        return ($prepareEmployee && $prepareStudent);
    }

    private static function PrepareEmployee()
    {
        $navRepo = new Navigation;
        $syncRepo = new RepositorySync;
        $schoolRepo = new School;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $m365UserRepo = new RepositoryUser;
        $informatEmployeeRepo = new Employee;

        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;
        $_status = $_settings['informat']['ownfield']['status'];
        $_firstName = $_settings['informat']['ownfield']['createEmailWith'];
        $_photo = General::convert($_settings['photo']['employee'], 'bool');

        $currentEmployees = $m365UserRepo->getAllEmployees(['id', 'employeeId', 'mail', 'accountEnabled', 'signInActivity', 'givenName', 'surname', 'displayName', 'onPremisesSamAccountName', 'onPremisesUserPrincipalName', 'companyName', 'department', 'jobTitle', 'memberOf', 'onPremisesExtensionAttributes']);
        $currentEmployees = Arrays::filter($currentEmployees, fn($ce) => Strings::equal($ce::class, \Microsoft\Graph\Generated\Models\User::class));
        $informatEmployees = $informatEmployeeRepo->get();
        // $informatEmployees = Arrays::filter($informatEmployees, fn($e) => $e->informatId == 15045);

        foreach ($informatEmployees as $informatEmployee) {
            $sync = $syncRepo->getByEmployeeId($informatEmployee->informatId) ?? new ObjectSync;
            $sync->type = "E";
            $sync->employeeId = $informatEmployee->informatId;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->ou = null;
            $sync->password = null;
            $sync->thumbnailPhoto = null;

            $m365User = Arrays::firstOrNull(Arrays::filter($currentEmployees, fn($cs) => Strings::equal($cs->getEmployeeId(), $informatEmployee->informatId) || Strings::equal($cs->getEmployeeId(), "P{$informatEmployee->informatId}")));
            $inService = Strings::equal(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_status))->value, "IN DIENST");

            $GivenName = (Strings::equalsIgnoreCase(($informatEmployeeOwnfieldRepo->getByInformatEmployeeIdSectionAndName($informatEmployee->id, 2, $_firstName)->value ?: "Officiële voornaam"), "officiële voornaam") ? $informatEmployee->firstName : $informatEmployee->extraFirstName);
            $DisplayName = Input::createDisplayName($_settings['format']['displayName'], $GivenName, $informatEmployee->name);
            $EmailAddress = Input::createEmail($_settings['format']['email'], $GivenName, $informatEmployee->name, EMAIL_SUFFIX);
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

            foreach ($_settings['default']['memberOf']['employee'] as $mof) {
                if (!is_array($mof)) $mof = [$mof];

                foreach ($schools as $school) {
                    $school = $schoolRepo->getByName($school);
                    if (Strings::isBlank($school->adSecGroupPart)) continue;
                    $_mof = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof[0]);

                    if ($m365User) {
                        if (!Arrays::contains($memberOfM365, $_mof)) $MemberOf[] = $_mof;
                    } else $MemberOf[] = $_mof;
                }
            }

            $MemberOf = array_unique($MemberOf);

            $departments = Arrays::map($schools, fn($d) => $schoolRepo->getByName($d)->name);
            $departments = implode(", ", $departments);

            $jobtitles = Arrays::map($functions, fn($f) => Arrays::first(explode(' (', $f->value)));
            $jobtitles = array_unique(array_values($jobtitles));
            $jobtitles = implode(", ", $jobtitles);

            $_functions = [];
            foreach ($functions as $function) {
                $school = $schoolRepo->getByName(Arrays::first(explode(" - ", $function->name)));
                $functionCode = str_replace(")", "", Arrays::last(explode("(", $function->value)));

                if (!Arrays::keyExists($_functions, $school->adJobTitlePrefix)) $_functions[$school->adJobTitlePrefix] = [];
                $_functions[$school->adJobTitlePrefix][] = $functionCode;
            }

            if (!empty($_functions)) {
                $ea1 = [];

                foreach ($_functions as $_school => $_codes) {
                    foreach ($_codes as $_code) {
                        $ea1[] = "{$_school}:{$_code}";
                    }
                }

                $OtherAttributes["extensionAttribute1"] = implode(" ", $ea1);
                if (Strings::equal($OtherAttributes["extensionAttribute1"], $m365User?->getOnPremisesExtensionAttributes()->getExtensionAttribute1()) || Strings::isBlank($OtherAttributes['extensionAttribute1'])) unset($OtherAttributes["extensionAttribute1"]);
            }

            if ($_photo && FileSystem::PathExists(LOCATION_IMAGE . "/informat/employee/{$informatEmployee->informatGuid}.jpg")) {
                if ((time() - filemtime(LOCATION_IMAGE . "/informat/employee/{$informatEmployee->informatGuid}.jpg")) < 1200)
                    $sync->thumbnailPhoto = FileSystem::GetDownloadLink(LOCATION_IMAGE . "/informat/employee/{$informatEmployee->informatGuid}.jpg");
            }

            $CompanyName = $_settings['default']['companyName']["employee"];
            if (Strings::contains($m365User?->getCompanyName(), "COLTD")) $CompanyName = "COLTD, {$CompanyName}";

            // Not in M365 - Active - Create
            if (
                !$m365User &&
                $informatEmployee->active &&
                $inService
            ) {
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
                $sync->otherAttributes = (is_null($MemberOf) || empty($OtherAttributes)) ? null : $OtherAttributes;
                $sync->password = User::generatePassword();
                $sync->ou = $_settings['default']['ou']['employee'];
            }
            // Disabled in M365 - Active - Enable
            else if (
                $m365User &&
                $m365User->getAccountEnabled() == false &&
                $informatEmployee->active &&
                $inService
            ) {
                $sync->action = "E";
                $sync->givenName = Strings::equal($GivenName, $m365User->getGivenName()) ? null : $GivenName;
                $sync->surname = Strings::equal($informatEmployee->name, $m365User->getSurname()) ? null : $informatEmployee->name;
                $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                $sync->department = Strings::equal($departments, $m365User->getDepartment()) ? null : ($departments ?: null);
                $sync->jobTitle = Strings::equal($jobtitles, $m365User->getJobTitle()) ? null : ($jobtitles ?: null);
                $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                $sync->otherAttributes = (is_null($MemberOf) || empty($OtherAttributes)) ? null : $OtherAttributes;
                $sync->password = User::generatePassword();
                $sync->ou = $_settings['default']['ou']['employee'];
            }
            // Enabled in M365 - Active - Update
            else if (
                $m365User &&
                $m365User->getAccountEnabled() == true  &&
                (
                    $informatEmployee->active ||
                    $inService
                )
            ) {
                $sync->action = "U";
                $sync->givenName = Strings::equal($GivenName, $m365User->getGivenName()) ? null : $GivenName;
                $sync->surname = Strings::equal($informatEmployee->name, $m365User->getSurname()) ? null : $informatEmployee->name;
                $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                $sync->department = Strings::equal($departments, $m365User->getDepartment()) ? null : ($departments ?: null);
                $sync->jobTitle = Strings::equal($jobtitles, $m365User->getJobTitle()) ? null : ($jobtitles ?: null);
                $sync->memberOf = (is_null($MemberOf) || empty($MemberOf)) ? null : $MemberOf;
                $sync->otherAttributes = (is_null($MemberOf) || empty($OtherAttributes)) ? null : $OtherAttributes;
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
                $sync->action = "D";
                $sync->givenName = null;
                $sync->surname = null;
                $sync->displayName = null;
                $sync->companyName = null;
                $sync->department = null;
                $sync->memberOf = null;
                $sync->thumbnailPhoto = null;
            } else $sync->action = null;

            if (!Strings::equal($sync->action, "D") && $sync->noUpdate()) $sync->action = null;
            $sync->setEmail = $sync->emailAddress ?: $m365User?->getMail();
            $sync->setPassword = $sync->password ?: $sync->setPassword;

            $nId = $syncRepo->set($sync);
            if (!$sync->id) $sync = Arrays::firstOrNull($syncRepo->get($nId));

            if (Arrays::contains(["C", "E"], $sync->action)) self::createNewEmployeeMail($sync);
        }

        return true;
    }

    private static function PrepareStudent()
    {
        $navRepo = new Navigation;
        $syncRepo = new RepositorySync;
        $schoolRepo = new School;
        $instituteRepo = new Institute;
        $m365UserRepo = new RepositoryUser;
        $informatStudentRepo = new Student;
        $informatRegistrationRepo = new Registration;
        $informatRegistrationClassRepo = new RegistrationClass;
        $informatClassgroupRepo = new ClassGroup;
        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;
        $_minDepartmentCodes = $_settings['minimum']['departmentCode'];
        $_minGrade = General::convert($_settings['minimum']['grade'], 'int');
        $_minYear = General::convert($_settings['minimum']['year'], 'int');
        $_photo = General::convert($_settings['photo']['student'], 'bool');

        $create = $update = $enable = $disable = [];

        $currentStudents = $m365UserRepo->getAllStudents(['id', 'employeeId', 'mail', 'accountEnabled', 'signInActivity', 'givenName', 'surname', 'displayName', 'onPremisesSamAccountName', 'onPremisesUserPrincipalName', 'companyName', 'department', 'jobTitle', 'memberOf', 'onPremisesExtensionAttributes', 'onPremisesDistinguishedName']);
        $currentStudents = Arrays::filter($currentStudents, fn($ce) => Strings::equal($ce::class, \Microsoft\Graph\Generated\Models\User::class));
        $informatStudents = $informatStudentRepo->get();

        foreach ($informatStudents as $informatStudent) {
            $sync = $syncRepo->getByEmployeeId($informatStudent->informatId) ?? new ObjectSync;
            $sync->type = "S";
            $sync->employeeId = $informatStudent->informatId;
            $sync->emailAddress = null;
            $sync->samAccountName = null;
            $sync->userPrincipalName = null;
            $sync->jobTitle = null;
            $sync->otherAttributes = null;
            $sync->password = null;
            $sync->thumbnailPhoto = null;

            $m365User = Arrays::firstOrNull(Arrays::filter($currentStudents, fn($cs) => Strings::equal($cs->getEmployeeId(), $informatStudent->informatId) || Strings::equal($cs->getEmployeeId(), "L{$informatStudent->informatId}")));
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

            $DisplayName = Input::createDisplayName($_settings['format']['displayName'], $informatStudent->firstName, $informatStudent->name);
            $EmailAddress = Input::createEmail($_settings['format']['email'], $informatStudent->firstName, $informatStudent->name, EMAIL_SUFFIX_STUDENT);

            $CompanyName = str_replace("{{school:name}}", $school->name, $_settings['default']['companyName']["student"]);
            $OU = str_replace("{{school:adOuPart}}", $school->adOuPart, $_settings['default']['ou']['student']);

            $MemberOf = [];

            if ($m365User) {
                $memberOfM365 = $m365UserRepo->getMemberOf($m365User->getId(), ['onPremisesDomainName', 'displayName']);
                $memberOfM365 = Arrays::filter($memberOfM365, fn($m) => $m->getOnPremisesDomainName() !== null);
                $memberOfM365 = Arrays::map($memberOfM365, fn($m) => $m->getDisplayName());

                $m365OU = explode(",", $m365User?->getOnPremisesDistinguishedName());
                array_shift($m365OU);
                $m365OU = implode(",", $m365OU);
            }

            foreach ($_settings['default']['memberOf']['student'] as $mof) {
                if (is_null($school->adSecGroupPart)) continue;

                if ($m365User) {
                    $mof = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof);
                    if (!Arrays::contains($memberOfM365, $mof)) $MemberOf[] = $mof;
                } else $MemberOf[] = str_replace("{{school:adSecGroupPart}}", $school->adSecGroupPart, $mof);
            }
            $MemberOf = array_unique($MemberOf);

            if ($_photo && FileSystem::PathExists(LOCATION_IMAGE . "/informat/student/{$informatStudent->informatGuid}.jpg")) {
                if ((time() - filemtime(LOCATION_IMAGE . "/informat/student/{$informatStudent->informatGuid}.jpg")) < 1200)
                    $sync->thumbnailPhoto = FileSystem::GetDownloadLink(LOCATION_IMAGE . "/informat/student/{$informatStudent->informatGuid}.jpg");
            }

            if (
                $currentRegistration &&
                Arrays::contains($_minDepartmentCodes, $currentRegistration->departmentCode) &&
                $currentRegistration->grade >= $_minGrade &&
                $currentRegistration->year >= $_minYear &&
                is_null($currentRegistration->end)
            ) {
                // Not in M365 - Registered - Create
                if (!$m365User) {
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
                    $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
                    $sync->password = User::generatePassword();
                    $sync->ou = $OU;
                }
                // Disabled in M365 - Registered - Enable
                else if (
                    $m365User &&
                    $m365User->getAccountEnabled() == false
                ) {
                    $sync->action = "E";
                    $sync->givenName = Strings::equal($informatStudent->firstName, $m365User->getGivenName()) ? null : $informatStudent->firstName;
                    $sync->surname = Strings::equal($informatStudent->name, $m365User->getSurname()) ? null : $informatStudent->name;
                    $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                    $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                    $sync->department = Strings::equal($class->code, $m365User->getDepartment()) ? null : ($class->code ?: null);
                    $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
                    $sync->password = User::generatePassword();
                }
                // Enabled in M365 - Registered - Update
                else {
                    $sync->action = "U";
                    $sync->givenName = Strings::equal($informatStudent->firstName, $m365User->getGivenName()) ? null : $informatStudent->firstName;
                    $sync->surname = Strings::equal($informatStudent->name, $m365User->getSurname()) ? null : $informatStudent->name;
                    $sync->displayName = Strings::equal($DisplayName, $m365User->getDisplayName()) ? null : $DisplayName;
                    $sync->companyName = Strings::equal($CompanyName, $m365User->getCompanyName()) ? null : $CompanyName;
                    $sync->department = Strings::equal($class->code, $m365User->getDepartment()) ? null : ($class->code ?: null);
                    $sync->ou = Strings::equal($OU, $m365OU) ? null : $OU;
                    $sync->memberOf = empty($MemberOf) ? null : $MemberOf;
                }
            } else {
                // Enabled in M365 - Unregistered/No account needed - Disable
                // if ($m365User && $m365User->getAccountEnabled() == true && !is_null($currentRegistration->end) && Clock::now()->isAfterOrEqualTo(Clock::at($currentRegistration->end))) {
                if (
                    $m365User &&
                    $m365User->getAccountEnabled() == true
                ) {
                    $sync->action = "D";
                    $sync->givenName = null;
                    $sync->surname = null;
                    $sync->displayName = null;
                    $sync->companyName = null;
                    $sync->department = null;
                    $sync->memberOf = null;
                    $sync->thumbnailPhoto = null;
                } else $sync->action = null;
            }

            if (!Strings::equal($sync->action, "D") && $sync->noUpdate()) $sync->action = null;
            $sync->setEmail = $sync->emailAddress ?: $m365User?->getMail();
            $sync->setPassword = $sync->password ?: $sync->setPassword;

            $nId = $syncRepo->set($sync);
            if (!$sync->id) $sync = Arrays::firstOrNull($syncRepo->get($nId));

            if ($sync->action == "C") $create[$school->id][] = $sync;
            else if ($sync->action == "U") $update[$school->id][] = $sync;
            else if ($sync->action == "E") $enable[$school->id][] = $sync;
            else if ($sync->action == "D") $disable[$school->id][] = $sync;
        }

        self::createStudentMail($create, $update, $enable, $disable);

        return true;
    }

    private static function createNewEmployeeMail($sync)
    {
        $navRepo = new Navigation;
        $informatEmployeeRepo = new Employee;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;

        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;
        $_mainSchool = $_settings['informat']['ownfield']['mainSchool'];
        $_mailType = $_settings['informat']['mailType'];

        $employee = $informatEmployeeRepo->getByInformatId($sync->employeeId);

        $subject = $_settings['mail']['template']['employee']['subject'];
        $body = $_settings['mail']['template']['employee']['body'];

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

    private static function createStudentMail($create = [], $update = [], $enable = [], $disable = [])
    {
        $navRepo = new Navigation;
        $schoolRepo = new School;
        $informatStudentRepo = new Student;
        $informatRegistrationRepo = new Registration;
        $informatRegistrationClassRepo = new RegistrationClass;
        $informatClassgroupRepo = new ClassGroup;

        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $_settings = Arrays::first($navRepo->getByParentIdAndLink(0, 'sync'))->settings;

        foreach ($schoolRepo->get() as $school) {
            if (!Arrays::keyExists($create, $school->id) && !Arrays::keyExists($update, $school->id) && !Arrays::keyExists($enable, $school->id) && !Arrays::keyExists($disable, $school->id)) continue;

            $subject = $_settings['mail']['template']['student']['subject'];
            $body = $_settings['mail']['template']['student']['body'];

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
