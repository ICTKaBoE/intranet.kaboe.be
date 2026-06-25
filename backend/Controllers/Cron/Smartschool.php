<?php

namespace Controllers\Cron;

use Database\Object\School\Course as SchoolCourse;
use Database\Object\School\CourseInformatEmployeeStudentClassgroup as SchoolCourseInformatEmployeeStudentClassgroup;
use Database\Object\School\CourseInformatStudent as SchoolCourseInformatStudent;
use Database\Repository\General\Schoolyear;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Informat\ClassGroupTeacher;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\Student;
use Database\Repository\School\Course;
use Database\Repository\School\CourseInformatEmployeeStudentClassgroup;
use Database\Repository\School\CourseInformatStudent;
use Database\Repository\School\School;
use Database\Repository\Smartschool\Message;
use Database\Repository\Smartschool\MessageReceiver;
use Database\Repository\Source;
use Database\Repository\User\User as RepositoryUser;
use Helpers\CString;
use Helpers\Log;
use M365\Repository\User;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Security\GUID;
use Smartschool\Repository\AllAccountsExtended;
use Smartschool\Repository\AllGroupsAndClasses;
use Smartschool\Repository\SkoreClassTeacherCourseRelation;
use Smartschool\Repository\UserDetails;
use Smartschool\Smartschool as SmartschoolSmartschool;

abstract class Smartschool
{
    public static function Send()
    {
        $return = true;
        $repo = new Message;
        $recRepo = new MessageReceiver;

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering messages to be sent...");
        $messages = $repo->get();
        $messages = Arrays::filter($messages, fn($m) => Strings::isBlank($m->sentDateTime));
        $messages = Arrays::filter($messages, fn($m) => Clock::now()->isAfterOrEqualTo(Clock::at($m->sendAfterDateTime)));
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", count($messages) . " messages to be sent!");

        foreach ($messages as $message) {
            $receivers = $recRepo->getByMessageId($message->id);
            // $attachments = $attRepo->getByMailId($mail->id);

            foreach ($receivers as $receiver) {
                try {
                    SmartschoolSmartschool::SendMessage($message->sourceId, $receiver->username, $message->subject, $message->body, account: $receiver->account, copyToLvs: $receiver->copyToLvs);
                } catch (\Exception $e) {
                    $return = false;
                }
            }

            $message->sentDateTime = Clock::nowAsString("Y-m-d H:i:s");
            $repo->set($message);
        }

        return $return;
    }

    public static function ImportCourses()
    {
        $courseRepo = new Course;
        $courseISRepo = new CourseInformatStudent;
        $courseIESCRepo = new CourseInformatEmployeeStudentClassgroup;
        $informatStudentRepo = new Student;
        $informatEmployeeRepo = new Employee;
        $informatClassgroupRepo = new ClassGroup;
        $sourceRepo = new Source;

        $courseISRepo->delete();
        $courseIESCRepo->delete();

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering SkoreClassTeacherCourseRelations...");
        $sources = array_values(Arrays::filter($sourceRepo->get(), fn($s) => Strings::startsWith($s->id, "smartschool-so")));

        foreach ($sources as $source) {
            $courses = (new SkoreClassTeacherCourseRelation($source->id))->get();

            foreach ($courses as $course) {
                if (!$course->klasnaam || Strings::equal($course->klasnaam, 0)) continue;
                $courseName = explode(" [", $course->vaknaam)[0];

                // Save course
                $courseItem = $courseRepo->getByName($courseName) ?? (new SchoolCourse);
                $courseItem->name = $courseName;
                $nId = $courseRepo->set($courseItem);
                $courseId = $courseItem->id ?? $nId;
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, ($courseItem->id ? "INFO" : "WARN"), "Course name: {$courseName}" . ($courseItem->id ? "" : "... Created!"));

                // Link student
                foreach ($course->leerlingen['leerling'] as $student) {
                    if (is_null($student->internnummer) || is_array($student->internnummer)) continue;
                    $informatStudent = $informatStudentRepo->getByInformatId(CString::getDigitsOnly($student->internnummer));

                    if ($informatStudent) {
                        $courseInformatStudent = $courseISRepo->getBySchoolCourseIdAndInformatStudentId($courseId, $informatStudent->id) ?? (new SchoolCourseInformatStudent);
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, ($courseInformatStudent->schoolCourseId ? "INFO" : "WARN"), "Student Link - Student ID: {$informatStudent->id}; Course ID: {$courseId}");

                        if (!$courseInformatStudent->schoolCourseId) {
                            $courseInformatStudent->schoolCourseId = $courseId;
                            $courseInformatStudent->informatStudentId = $informatStudent->id;
                            $courseISRepo->set($courseInformatStudent);
                        }
                    }
                }

                // Link teacher, student and class
                if (!is_null($course->internnummer) && !is_array($course->internnummer)) {
                    $informatEmployee = $informatEmployeeRepo->getByInformatId(CString::getDigitsOnly($course->internnummer));
                    $informatClassgroup = $informatClassgroupRepo->getBySchoolyearAndCode(_CURRENT_SCHOOLYEAR_, $course->klasnaam);

                    foreach ($course->leerlingen['leerling'] as $student) {
                        if (is_null($student->internnummer) || is_array($student->internnummer)) continue;
                        $informatStudent = $informatStudentRepo->getByInformatId(CString::getDigitsOnly($student->internnummer));

                        if ($informatEmployee && $informatClassgroup && $informatStudent) {
                            $secs = $courseIESCRepo->getBySchoolCourseIdInformatEmployeeIdInformatStudentIdAndInformatClassgroupId($courseId, $informatEmployee->id, $informatStudent->id, $informatClassgroup->id) ?? (new SchoolCourseInformatEmployeeStudentClassgroup);
                            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, ($secs->schoolCourseId ? "INFO" : "WARN"), "Link - Employee ID: {$informatEmployee->id}; Student ID: {$informatStudent->id}; Course ID: {$courseId}; Classgroup ID: {$informatClassgroup->id}");

                            if (!$secs->schoolCourseId) {
                                $secs->schoolCourseId = $courseId;
                                $secs->informatEmployeeId = $informatEmployee->id;
                                $secs->informatStudentId = $informatStudent->id;
                                $secs->informatClassgroupId = $informatClassgroup->id;
                                $courseIESCRepo->set($secs);
                            }
                        }
                    }
                };

                Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            }
        }

        return true;
    }

    public static function SyncGroups()
    {
        $sourceRepo = new Source;

        $sources = array_values(Arrays::filter($sourceRepo->get(), fn($s) => Strings::startsWith($s->id, "smartschool")));

        foreach ($sources as $source) {
            $errorCodes = SmartschoolSmartschool::GetErrorCodes($source->id);
            try {
                Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering Groups for {$source->id}...");

                $allGroupsAndClasses = (new AllGroupsAndClasses($source->id))->get();
                $groups = Arrays::filter($allGroupsAndClasses, fn($g) => is_string($g['code']) && GUID::validate(strtoupper($g['code'])));
                $groups = array_values($groups);

                foreach ($groups as $group) {
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Processing Group {$group['name']}...");

                    $smsUsers = (new AllAccountsExtended($source->id))->get($group["code"], "1");
                    $m365Users = (new User)->getGroupMembersByGroupId($group['code'], ["userPrincipalName", "surname", "givenName", "employeeId"]);
                    $m365Users = Arrays::filter($m365Users, fn($u) => Strings::equal($u::class, \Microsoft\Graph\Generated\Models\User::class));
                    $m365Users = Arrays::map($m365Users, fn($u) => [
                        "userPrincipalName" => $u->getUserPrincipalName(),
                        "surname" => $u->getSurname(),
                        "givenName" => $u->getGivenName(),
                        "employeeId" => $u->getEmployeeId()
                    ]);
                    $inSmsGroupUsers = Arrays::map($smsUsers, fn($u) => $u->gebruikersnaam);
                    $m365UsersQuick = Arrays::map($m365Users, fn($u) => $u['userPrincipalName']);

                    $addToSmsGroup = array_diff($m365UsersQuick, $inSmsGroupUsers);
                    $removeFromSmsGroup = array_diff($inSmsGroupUsers, $m365UsersQuick);

                    foreach ($addToSmsGroup as $username) {
                        Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                        $smsUser = (new UserDetails($source->id))->get($username);

                        if (is_int($smsUser)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $errorCodes[$smsUser] ?? "Unknown error code: {$smsUser}");

                        $m365User = Arrays::firstOrNull(Arrays::filter($m365Users, fn($u) => Strings::equal($u['userPrincipalName'], $username)));
                        if ($m365User) {
                            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Adding user {$username} to group...");
                            $addResult = SmartschoolSmartschool::AddUserToGroup($source->id, $username, $group['code']);
                            if (is_int($addResult) && $addResult !== 0) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $errorCodes[$addResult] ?? "Unknown error code: {$addResult}");
                        } else Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "User '{$username}' not found in M365 group members...");
                    }

                    foreach ($removeFromSmsGroup as $username) {
                        Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Removing user '{$username}' from group...");
                        $smsUser = (new UserDetails($source->id))->get($username);

                        if (is_int($smsUser)) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $errorCodes[$smsUser] ?? "Unknown error code: {$smsUser}");
                        else {
                            $removeResult = SmartschoolSmartschool::RemoveUserFromGroup($source->id, $username, $group['code']);
                            if (is_int($removeResult) && $removeResult !== 0) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $errorCodes[$removeResult] ?? "Unknown error code: {$removeResult}");
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $e->getMessage());
            }
        }

        return true;
    }

    public static function SyncClassTeachers()
    {
        $sourceRepo = new Source;
        $classgroupRepo = new ClassGroup;
        $classgroupTeacherRepo = new ClassGroupTeacher;
        $userRepo = new RepositoryUser;
        $schoolyear = (new Schoolyear)->getCurrent();
        $sources = Arrays::filter($sourceRepo->get(), fn($s) => Strings::startsWith($s->id, "smartschool"));

        foreach ($sources as $source) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Source: {$source->id}");
            $errorCodes = SmartschoolSmartschool::GetErrorCodes($source->id);
            $classes = (new AllGroupsAndClasses($source->id))->get();
            $classes = Arrays::uniqueBy(Arrays::filter($classes, fn($c) => Strings::equal('K', $c['type'])), 'name');

            foreach ($classes as $class) {
                $classgroups = $classgroupRepo->getAllBySchoolyearAndCode($schoolyear->name, $class['name']);

                foreach ($classgroups as $classgroup) {
                    Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
                    $sync = $classgroup->linked->schoolInstitute->linked->school->smsSyncClassTeachers ?: $classgroup->linked->schoolInstitute->linked->school->linked->parentSchool->smsSyncClassTeachers;
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "{$classgroup->linked->schoolInstitute->linked->school->name} - {$class['name']}" . ($sync ? "" : "... NO SYNC"));
                    if (!$sync) continue;

                    $classgroupTeachers = $classgroupTeacherRepo->getByInformatClassgroupId($classgroup->id);
                    $usernames = [];

                    foreach ($classgroupTeachers as $classgroupTeacher) {
                        $username = ($userRepo->getByInformatEmployeeId($classgroupTeacher->linked->informatEmployee->informatId) ?: $userRepo->getByInformatEmployeeId("P{$classgroupTeacher->linked->informatEmployee->informatId}"))?->username;

                        if ($username) {
                            $usernames[] = $username;
                        }
                    }

                    $usernames = implode(",", $usernames);
                    Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Link " . implode(", ", explode(",", $usernames)) . " to class...");
                    $result = SmartschoolSmartschool::ChangeGroupOwners($source->id, $class['code'], $usernames);
                    if (is_int($result) && $result !== 0) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", $errorCodes[$result] ?? "Unknown error code: {$result}");
                }
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return true;
    }
}
