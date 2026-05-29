<?php

namespace Controllers\Cron;

use Helpers\Log;
use Security\User;
use Security\Input;
use Helpers\General;
use Security\FileSystem;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Mail\Mail;
use Database\Repository\Mail\Receiver;
use Database\Repository\School\School;
use Database\Repository\Informat\Student;
use Database\Repository\School\Institute;
use Database\Object\Mail\Mail as MailMail;
use Database\Repository\Informat\Employee;
use Database\Repository\Navigation\Setting;
use M365\Repository\User as RepositoryUser;
use Database\Object\Sync\Sync as ObjectSync;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\Registration;
use Database\Repository\Navigation\Navigation;
use Database\Repository\Informat\EmployeeEmail;
use Database\Object\Mail\Receiver as MailReceiver;
use Database\Repository\Informat\EmployeeOwnfield;
use Database\Repository\Informat\RegistrationClass;
use Database\Repository\Sync\Sync as RepositorySync;

abstract class Sync
{

    /* ---------------------------- PUBLIC ENTRY ----------------------------- */
    public static function Prepare()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Starting sync...");
        $ok1 = self::PrepareEmployee();
        Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        $ok2 = self::PrepareStudent();
        return ($ok1 && $ok2);
    }

    /* ========================== REUSABLE HELPERS ========================== */

    // Generic null-coalescing helper with fallback pipeline
    private static function pick(...$values)
    {
        foreach ($values as $v) {
            if (!is_null($v) && $v !== "") return $v;
        }
        return null;
    }

    // Reusable function to build displayName/email
    private static function buildNameAndMail($formatDisplay, $formatEmail, $firstName, $lastName, $suffix)
    {
        $display = Input::createDisplayName($formatDisplay, $firstName, $lastName);
        $email = Input::createEmail($formatEmail, $firstName, $lastName, $suffix);
        return [$display, $email];
    }

    // Normalizes OU replacement
    private static function replaceOu($template, $school)
    {
        return str_replace(
            ["{{school:adOuPart}}", "{{school:adOuPartUpper}}"],
            [$school->adOuPart, strtoupper($school->adOuPart)],
            $template
        );
    }

    // Determines if a photo should be used and returns URL or null
    private static function resolvePhoto($path)
    {
        if (!FileSystem::PathExists($path)) return null;
        if (time() - filemtime($path) > 1200) return null;
        return FileSystem::GetDownloadLink($path);
    }

    // Generic write routine for sync properties
    private static function applyField($value, $current = null)
    {
        if (Strings::equal($value, $current)) return null;
        return Strings::trimToNull($value);
    }

    private static function PrepareEmployee()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Preparing employees...");

        $settingRepo  = new Setting;
        $syncRepo     = new RepositorySync;
        $schoolRepo   = new School;
        $ownRepo      = new EmployeeOwnfield;
        $m365Repo     = new RepositoryUser;
        $employeeRepo = new Employee;

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");

        $statusKey     = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.status")->value;
        $firstNameKey  = $settingRepo->getByNavigationIdAndKey($navigation->id, "informat.ownfield.createEmailWith")->value;
        $photoEnabled  = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "photo.employee")->value, 'bool');

        /* ---------------------- GET ALL M365 + INFORMAT EMPLOYEES ---------------------- */
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering M365 employees...");
        $currentEmployees = $m365Repo->getAllEmployees([
            'id',
            'employeeId',
            'mail',
            'accountEnabled',
            'signInActivity',
            'givenName',
            'surname',
            'displayName',
            'onPremisesSamAccountName',
            'onPremisesUserPrincipalName',
            'companyName',
            'department',
            'jobTitle',
            'memberOf',
            'onPremisesExtensionAttributes'
        ]);

        $currentEmployees = Arrays::filter(
            $currentEmployees,
            fn($u) =>
            Strings::equal($u::class, \Microsoft\Graph\Generated\Models\User::class)
        );

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Found " . count($currentEmployees) . " employees");
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering Informat employees...");

        $informat = $employeeRepo->get();
        // $informat = Arrays::filter($informat, fn($e) => $e->informatId == 12897);
        // $informat = Arrays::filter($informat, fn($e) => $e->instituteId == 0 && ($e->linked->institute->linked->school->sync || $e->linked->institute->linked->school->linked->parentSchool->sync));
        // Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Filtered to " . count($informat));

        /* ----------------------------- PROCESS EMPLOYEES ------------------------------ */
        foreach ($informat as $emp) {
            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Employee: {$emp->informatId} - {$emp->name} {$emp->firstName}");

            if ($emp->instituteId !== 0 && !($emp->linked->institute->linked->school->sync || $emp->linked->institute->linked->school->linked->parentSchool->sync)) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Sync not allowed, skipping");
                continue;
            }

            /* ---------- Load or Init Sync Object ---------- */
            $sync = $syncRepo->getByEmployeeId($emp->informatId) ?? new ObjectSync;
            $sync->type = "E";
            $sync->action = null;
            $sync->employeeId = $emp->informatId;

            /* ---------- Identify matching M365 user ---------- */
            $m365 = Arrays::firstOrNull(Arrays::filter($currentEmployees, fn($u) => Strings::equal($u->getEmployeeId(), $emp->informatId) || Strings::equal($u->getEmployeeId(), "P{$emp->informatId}")));

            /* ---------- Check active / status ---------- */
            $ownFieldStatus = $ownRepo->getByInformatEmployeeIdSectionAndName($emp->id, 2, $statusKey)?->value;
            $inService = is_null($ownFieldStatus) ? General::convert($emp->active, "boolean") : Strings::equal($ownFieldStatus, "IN DIENST");

            /* ---------- Determine GivenName ---------- */
            $firstNameSource = $ownRepo->getByInformatEmployeeIdSectionAndName($emp->id, 2, $firstNameKey)?->value ?: "Voornaam";
            $givenName = Strings::equalsIgnoreCase($firstNameSource, "voornaam") ? $emp->firstName : $emp->extraFirstName;

            /* ---------- Build display + email ---------- */
            $fmtDisplay = $settingRepo->getByNavigationIdAndKey($navigation->id, "format.displayName")->value;
            $fmtEmail   = $settingRepo->getByNavigationIdAndKey($navigation->id, "format.email")->value;

            [$displayName, $emailAddress] =
                self::buildNameAndMail($fmtDisplay, $fmtEmail, $givenName, $emp->name, EMAIL_SUFFIX);

            /* ---------- Avoid duplicate emails ---------- */
            $postfix = 2;
            $parts = explode("@", $emailAddress);
            while (Arrays::firstOrNull(Arrays::filter($currentEmployees, fn($u) => Strings::equalsIgnoreCase($u->getMail(), $emailAddress)))) {
                $emailAddress = "{$parts[0]}{$postfix}@{$parts[1]}";
                $postfix++;
            }

            $samAccountName = substr(explode("@", $emailAddress)[0], 0, 20);

            /* ---------- Functions, schools, job titles, attributes ---------- */
            $functions = Arrays::filter($ownRepo->getByInformatEmployeeIdAndSection($emp->id, 2), fn($f) => Strings::contains($f->name, " - Functie "));
            $schoolNames = array_unique(Arrays::map($functions, fn($f) => Arrays::first(explode(" - ", $f->name))));
            $departments = implode(", ", Arrays::map($schoolNames, fn($sn) => $schoolRepo->getByName($sn)->name)) ?: null;
            $jobTitles = implode(", ", array_unique(Arrays::map(Arrays::filter($functions, fn($f) => !Strings::startsWith(Arrays::last(explode(' (', $f->value)), "!")), fn($f) => Arrays::first(explode(' (', $f->value))))) ?: null;

            /* ---------- MemberOf groups ---------- */
            $memberOf = [];

            $defaultGroups = explode(",", $settingRepo->getByNavigationIdAndKey($navigation->id, "default.memberOf.employee")->value);

            if ($m365) {
                $existing = $m365Repo->getMemberOf($m365->getId(), ['displayName']);
                $existing = Arrays::map($existing, fn($g) => $g?->getDisplayName());
            }

            foreach ($defaultGroups as $g) {
                $g = Strings::trimToNull($g);
                if (!$g) continue;

                foreach ($schoolNames as $sn) {
                    $s = $schoolRepo->getByName($sn);
                    if (!$s->adSecGroupPart) continue;

                    $_g = str_replace(["{{school:adSecGroupPart}}", "{{school:adOuPartUpper}}"], [$s->adSecGroupPart, strtoupper($s->adOuPart)], $g);
                    if (!$m365 || !Arrays::contains($existing, $_g)) $memberOf[] = $_g;
                }
            }

            $memberOf = array_unique($memberOf) ?: null;

            /* ---------- Badge ID + extension attributes ---------- */
            $badgeId = $ownRepo->getByInformatEmployeeIdSectionAndName($emp->id, 2, "Badge ID")?->value;
            $otherAttributes = [];

            // ExtensionAttribute1 = job title per school
            $funcCodes = [];
            foreach ($functions as $f) {
                $school = $schoolRepo->getByName(Arrays::first(explode(" - ", $f->name)));
                $code = str_replace(")", "", Arrays::last(explode("(", $f->value)));
                $code = str_replace("!", "", $code);
                $funcCodes[] = "{$school->adJobTitlePrefix}:{$code}";
            }
            if ($funcCodes) {
                $ea1 = implode(" ", $funcCodes);
                if (!$m365 || $ea1 !== $m365?->getOnPremisesExtensionAttributes()?->getExtensionAttribute1()) $otherAttributes["extensionAttribute1"] = $ea1;
            }

            if ($badgeId && (!$m365 || $badgeId !== $m365?->getOnPremisesExtensionAttributes()?->getExtensionAttribute15())) {
                $otherAttributes["extensionAttribute15"] = $badgeId;
                $otherAttributes["pager"] = $badgeId;
            }

            $otherAttributes = $otherAttributes ?: null;

            /* ---------- Company + OU ---------- */
            $schoolObject = $emp->linked->institute->linked->school;
            $companyName = self::pick($schoolObject->syncEmployeeCompanyName, $schoolObject->linked->parentSchool->syncEmployeeCompanyName);
            if ($m365 && Strings::contains($m365->getCompanyName(), "COLTD") && $companyName !== "COLTD") $companyName = "COLTD, {$companyName}";
            $employeeOU = self::pick($schoolObject->syncEmployeeOU, $schoolObject->linked->parentSchool->syncEmployeeOU);

            /* ---------- Photo ---------- */
            $photo = null;
            if ($photoEnabled) $photo = self::resolvePhoto(LOCATION_IMAGE . "/informat/employee/{$emp->informatGuid}.jpg");

            /* ---------- Determine ACTION: Create / Enable / Update / Disable ---------- */
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "M365: " . ($m365 ? "" : "NOT ") . "FOUND; M365 Enabled: " . ($m365 ? ($m365->getAccountEnabled() ? "YES" : "NO") : "N/A") . "; In Service OR Informat Active: " . ($inService ? "YES" : "NO"));

            if (!$m365 && $inService) $sync->action = "C";
            else if ($m365 && !$m365->getAccountEnabled() && $inService) $sync->action = "E";
            else if ($m365 && $m365->getAccountEnabled() && $inService) $sync->action = "U";
            else if ($m365 && $m365->getAccountEnabled() && !$inService) $sync->action = "D";

            /* ---------- Apply fields based on ACTION ---------- */
            switch ($sync->action) {
                case 'C': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Create employee");
                        $sync->action               = "C";
                        $sync->givenName            = Strings::trimToNull($givenName);
                        $sync->surname              = Strings::trimToNull($emp->name);
                        $sync->displayName          = Strings::trimToNull($displayName);
                        $sync->emailAddress         = $emailAddress;
                        $sync->samAccountName       = $samAccountName;
                        $sync->userPrincipalName    = $emailAddress;
                        $sync->companyName          = $companyName;
                        $sync->department           = $departments;
                        $sync->jobTitle             = $jobTitles;
                        $sync->memberOf             = $memberOf;
                        $sync->otherAttributes      = $otherAttributes;
                        $sync->password             = User::generatePassword();
                        $sync->thumbnailPhoto       = $photo;
                        $sync->ou                   = $employeeOU;
                    }
                    break;
                case 'E': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Enable employee");
                        $sync->password = User::generatePassword();

                        $sync->givenName    = self::applyField($givenName,      $m365->getGivenName());
                        $sync->surname      = self::applyField($emp->name,      $m365->getSurname());
                        $sync->displayName  = self::applyField($displayName,    $m365->getDisplayName());
                        $sync->companyName  = self::applyField($companyName,    $m365->getCompanyName());
                        $sync->department   = self::applyField($departments,    $m365->getDepartment());
                        $sync->jobTitle     = self::applyField($jobTitles,      $m365->getJobTitle());

                        $sync->memberOf        = $memberOf;
                        $sync->otherAttributes = $otherAttributes;
                        $sync->thumbnailPhoto  = $photo;
                    }
                    break;
                case 'U': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Update employee");

                        $sync->givenName    = self::applyField($givenName,      $m365->getGivenName());
                        $sync->surname      = self::applyField($emp->name,      $m365->getSurname());
                        $sync->displayName  = self::applyField($displayName,    $m365->getDisplayName());
                        $sync->companyName  = self::applyField($companyName,    $m365->getCompanyName());
                        $sync->department   = self::applyField($departments,    $m365->getDepartment());
                        $sync->jobTitle     = self::applyField($jobTitles,      $m365->getJobTitle());

                        $sync->memberOf        = $memberOf;
                        $sync->otherAttributes = $otherAttributes;
                        $sync->thumbnailPhoto  = $photo;
                    }
                    break;
                case 'D': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Disable employee");
                        $sync->givenName       = null;
                        $sync->surname         = null;
                        $sync->displayName     = null;
                        $sync->companyName     = null;
                        $sync->department      = null;
                        $sync->memberOf        = null;
                        $sync->thumbnailPhoto  = null;
                        $sync->ou              = null;
                    }
                    break;

                default: {
                        $sync->givenName            = null;
                        $sync->surname              = null;
                        $sync->displayName          = null;
                        $sync->emailAddress         = null;
                        $sync->samAccountName       = null;
                        $sync->userPrincipalName    = null;
                        $sync->companyName          = null;
                        $sync->department           = null;
                        $sync->jobTitle             = null;
                        $sync->memberOf             = null;
                        $sync->otherAttributes      = null;
                        $sync->password             = null;
                        $sync->thumbnailPhoto       = null;
                        $sync->ou                   = null;
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "No changes - skip");
                    }
                    break;
            }

            /* ---------- Skip if no changes ---------- */
            if ($sync->action !== "D" && $sync->noUpdate()) {
                $sync->action = null;
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "No changes - skip");
            } else if (!is_null($sync->action)) {
                foreach ($sync->toSqlArray() as $k => $v) if (!is_null($v)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$k}: {$v}");
            }

            /* ---------- Persist ---------- */
            $sync->setEmail    = $sync->emailAddress ?: $m365?->getMail();
            $sync->setPassword = $sync->password ?: $sync->setPassword;

            $id = $syncRepo->set($sync);
            if (!$sync->id) $sync = Arrays::firstOrNull($syncRepo->get($id));

            /* ---------- Mails ---------- */
            if (Arrays::contains(["C", "E"], $sync->action)) {
                // self::createNewEmployeeMail($sync);
                // self::createNewEmployeeToCentralMail($sync, []);
            } else if ($sync->action === "D") {
                // self::createDisableEmployeeMail($sync);
                // self::createDisableEmployeeToCentralMail($sync);
            }
        }

        return true;
    }

    private static function PrepareStudent()
    {
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Preparing students...");

        $settingRepo  = new Setting;
        $syncRepo     = new RepositorySync;
        $schoolRepo   = new School;
        $instituteRepo = new Institute;
        $m365Repo     = new RepositoryUser;

        $studentRepo  = new Student;
        $regRepo      = new Registration;
        $regClassRepo = new RegistrationClass;
        $classRepo    = new ClassGroup;

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");
        $photoEnabled = General::convert($settingRepo->getByNavigationIdAndKey($navigation->id, "photo.student")->value, 'bool');

        /* ------------------------------ Get all M365 ------------------------------ */
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering M365 students...");
        $current = $m365Repo->getAllStudents([
            'id',
            'employeeId',
            'mail',
            'accountEnabled',
            'signInActivity',
            'givenName',
            'surname',
            'displayName',
            'onPremisesSamAccountName',
            'onPremisesUserPrincipalName',
            'companyName',
            'department',
            'jobTitle',
            'memberOf',
            'onPremisesExtensionAttributes',
            'onPremisesDistinguishedName'
        ]);
        $current = Arrays::filter(
            $current,
            fn($u) =>
            Strings::equal($u::class, \Microsoft\Graph\Generated\Models\User::class)
        );
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Found " . count($current) . " students");

        /* ---------------------------- Get Informat students ---------------------------- */
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering Informat students...");
        $informat = $studentRepo->get();
        // $informat = Arrays::filter($informat, fn($s) => $s->instituteId != 0 && ($s->linked->institute->linked->school->sync || $s->linked->institute->linked->school->linked->parentSchool->sync));
        // Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Filtered to " . count($informat) . " students");

        /* -------------------------------- Process each student ------------------------------- */
        foreach ($informat as $st) {
            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Student: {$st->informatId} - {$st->name} {$st->firstName}");

            if ($st->instituteId !== 0 && !($st->linked->institute->linked->school->sync || $st->linked->institute->linked->school->linked->parentSchool->sync)) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Sync not allowed, skipping");
                continue;
            }

            /* ---------- Load or Init Sync ---------- */
            $sync = $syncRepo->getByEmployeeId($st->informatId) ?? new ObjectSync;
            $sync->type = "S";
            $sync->action = null;
            $sync->employeeId = $st->informatId;

            /* ---------- Determine if student exists in M365 ---------- */
            $m365 = Arrays::firstOrNull(Arrays::filter($current, fn($u) => Strings::equal($u->getEmployeeId(), $st->informatId) || Strings::equal($u->getEmployeeId(), "L{$st->informatId}")));

            /* ---------- Registration / class lookups ---------- */
            $regs = $regRepo->getByInformatStudentId($st->id);
            $regs = Arrays::filter($regs, fn($r) => $r->current && $r->status == 0);
            $regs = Arrays::orderBy($regs, "start");

            $currentReg = count($regs) ? Arrays::last($regs) : null;

            $regClasses = $currentReg ? $regClassRepo->getByInformatRegistrationId($currentReg->id) : [];
            $regClasses = Arrays::filter($regClasses, fn($rc) => $rc->current);
            $regClasses = Arrays::filter($regClasses, function ($rc) use ($classRepo) {
                $class = Arrays::firstOrNull($classRepo->get($rc->informatClassGroupId));
                return $class && $class->type === "C";
            });

            $currentRegClass = Arrays::firstOrNull($regClasses);

            $institute = $currentReg ? Arrays::firstOrNull($instituteRepo->get($currentReg->schoolInstituteId)) : null;
            $school = $institute ? Arrays::firstOrNull($schoolRepo->get($institute->schoolId)) : null;
            $class = $currentRegClass ? Arrays::firstOrNull($classRepo->get($currentRegClass->informatClassGroupId)) : null;

            /* ---------- DisplayName + email ---------- */
            $fmtDisplay = $settingRepo->getByNavigationIdAndKey($navigation->id, "format.displayName")->value;
            $fmtEmail   = $settingRepo->getByNavigationIdAndKey($navigation->id, "format.email")->value;

            [$displayName, $emailAddress] =
                self::buildNameAndMail($fmtDisplay, $fmtEmail, $st->firstName, $st->name, EMAIL_SUFFIX_STUDENT);

            /* Avoid duplicate emails */
            $postfix = 2;
            $parts = explode("@", $emailAddress);
            while (Arrays::firstOrNull(Arrays::filter($current, fn($u) => Strings::equalsIgnoreCase($u->getMail(), $emailAddress)))) {
                $emailAddress = "{$parts[0]}{$postfix}@{$parts[1]}";
                $postfix++;
            }

            $samAccount = substr(explode("@", $emailAddress)[0], 0, 20);

            /* ---------- Company + OU ---------- */
            $schoolObj = $st->linked->institute->linked->school;
            $companyName = self::pick($schoolObj->syncStudentCompanyName, $schoolObj->linked->parentSchool->syncStudentCompanyName);
            $ou = self::pick($schoolObj->syncStudentOU, $schoolObj->linked->parentSchool->syncStudentOU);

            if ($school) $ou = self::replaceOu($ou, $school);

            /* ---------- MemberOf ---------- */
            $memberOf = [];

            $defaultGroups = explode(",", $settingRepo->getByNavigationIdAndKey($navigation->id, "default.memberOf.student")->value);

            if ($m365) {
                $existing = $m365Repo->getMemberOf($m365->getId(), ['displayName']);
                $existing = Arrays::map($existing, fn($g) => $g->getDisplayName());
            }

            foreach ($defaultGroups as $g) {
                if (!$g) continue;

                if (!$school->adSecGroupPart) continue;

                $_g = str_replace(["{{school:adSecGroupPart}}", "{{school:adOuPartUpper}}"], [$school->adSecGroupPart, strtoupper($school->adOuPart)], $g);

                if (!$m365 || !Arrays::contains($existing, $_g)) $memberOf[] = $_g;
            }

            $memberOf = $memberOf ?: null;

            /* ---------- Photo ---------- */
            $photo = null;
            if ($photoEnabled) $photo = self::resolvePhoto(LOCATION_IMAGE . "/informat/student/{$st->informatGuid}.jpg");

            /* ---------- Determine Action (Create/Enable/Update/Disable) ---------- */
            $now = Clock::now();

            $isRegistered = $currentReg && (is_null($currentReg->end) || $now->isBeforeOrEqualTo(Clock::at($currentReg->end)));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "M365: " . ($m365 ? "" : "NOT ") . "FOUND; M365 Enabled: " . ($m365 ? ($m365->getAccountEnabled() ? "YES" : "NO") : "N/A") . "; Registered: " . ($isRegistered ? "YES" : "NO"));

            if ($isRegistered && !$m365) $sync->action = "C";
            elseif ($isRegistered && $m365 && !$m365->getAccountEnabled()) $sync->action = "E";
            elseif ($isRegistered && $m365 && $m365->getAccountEnabled()) $sync->action = "U";
            elseif (!$isRegistered && $m365 && $m365->getAccountEnabled()) $sync->action = "D";

            /* ---------- Apply fields based on ACTION ---------- */
            switch ($sync->action) {
                case 'C': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Create student");
                        $sync->givenName         = Strings::trimToNull($st->firstName);
                        $sync->surname           = Strings::trimToNull($st->name);
                        $sync->displayName       = $displayName;
                        $sync->emailAddress      = $emailAddress;
                        $sync->samAccountName    = $samAccount;
                        $sync->userPrincipalName = $emailAddress;
                        $sync->companyName       = $companyName;
                        $sync->department        = $class?->code;
                        $sync->memberOf          = $memberOf;
                        $sync->password          = User::generatePassword();
                        $sync->ou                = trim($ou);
                        $sync->thumbnailPhoto    = $photo;
                    }
                    break;
                case 'E': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Enable student");
                        $m365OU = explode(",", $m365?->getOnPremisesDistinguishedName());
                        array_shift($m365OU);
                        $m365OU = implode(",", $m365OU);

                        $sync->givenName    = self::applyField($st->firstName, $m365->getGivenName());
                        $sync->surname      = self::applyField($st->name,      $m365->getSurname());
                        $sync->displayName  = self::applyField($displayName,   $m365->getDisplayName());
                        $sync->companyName  = self::applyField($companyName,   $m365->getCompanyName());
                        $sync->department   = self::applyField($class?->code,  $m365->getDepartment());
                        $sync->ou           = self::applyField($ou,            $m365OU ?? null);

                        $sync->memberOf       = $memberOf;
                        $sync->password       = User::generatePassword();
                        $sync->thumbnailPhoto = $photo;
                    }
                    break;
                case 'U': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Update student");
                        $m365OU = explode(",", $m365?->getOnPremisesDistinguishedName());
                        array_shift($m365OU);
                        $m365OU = implode(",", $m365OU);

                        $sync->givenName    = self::applyField($st->firstName, $m365->getGivenName());
                        $sync->surname      = self::applyField($st->name,      $m365->getSurname());
                        $sync->displayName  = self::applyField($displayName,   $m365->getDisplayName());
                        $sync->companyName  = self::applyField($companyName,   $m365->getCompanyName());
                        $sync->department   = self::applyField($class?->code,  $m365->getDepartment());
                        $sync->ou           = self::applyField($ou,            $m365OU ?? null);

                        $sync->memberOf       = $memberOf;
                        $sync->thumbnailPhoto = $photo;
                    }
                    break;
                case 'D': {
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Disable student");
                        $sync->givenName      = null;
                        $sync->surname        = null;
                        $sync->displayName    = null;
                        $sync->companyName    = null;
                        $sync->department     = null;
                        $sync->memberOf       = null;
                        $sync->thumbnailPhoto = null;
                    }
                    break;
                default: {
                        $sync->givenName         = null;
                        $sync->surname           = null;
                        $sync->displayName       = null;
                        $sync->emailAddress      = null;
                        $sync->samAccountName    = null;
                        $sync->userPrincipalName = null;
                        $sync->companyName       = null;
                        $sync->department        = null;
                        $sync->memberOf          = null;
                        $sync->password          = null;
                        $sync->ou                = null;
                        $sync->thumbnailPhoto    = null;
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "No changes - skip");
                    }
                    break;
            }

            /* ---------- Skip if no changes needed ---------- */
            if ($sync->action !== "D" && $sync->noUpdate()) {
                $sync->action = null;
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "No changes - skip");
            } else if (!is_null($sync->action)) {
                foreach ($sync->toSqlArray() as $k => $v) if (!is_null($v)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$k}: {$v}");
            }

            /* ---------- Persist ---------- */
            $sync->setEmail    = $sync->emailAddress ?: $m365?->getMail();
            $sync->setPassword = $sync->password ?: $sync->setPassword;

            $id = $syncRepo->set($sync);
            if (!$sync->id) $sync = Arrays::firstOrNull($syncRepo->get($id));

            /* ---------- Prepare student summary mail ---------- */
            // (original call disabled in your source code — left untouched)
            // self::createStudentMail($create, $update, $enable, $disable);
        }

        return true;
    }

    private static function createNewEmployeeMail($sync)
    {
        $informatEmployeeRepo = new Employee;
        $informatEmployeeOwnfieldRepo = new EmployeeOwnfield;
        $mailRepo = new Mail;
        $mailReceiverRepo = new Receiver;
        $settingRepo = new Setting;

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");
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

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");
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

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");

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
            "Directeur-CoÃ¶rdinator KaBoE" => "dirco.kaboe@coltd.be",
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

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");

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
            "Directeur-CoÃ¶rdinator KaBoE" => "dirco.kaboe@coltd.be",
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

        $navigation = (new Navigation)->getByLinkAndType('sync', "M");

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
