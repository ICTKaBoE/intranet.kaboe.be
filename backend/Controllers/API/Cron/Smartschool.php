<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use Helpers\CString;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\Source;
use Database\Repository\School\Course;
use Database\Repository\Informat\Student;
use Database\Repository\Informat\Employee;
use Database\Repository\Informat\ClassGroup;
use Database\Repository\Smartschool\Message;
use Database\Object\School\Course as SchoolCourse;
use Database\Repository\Smartschool\MessageReceiver;
use Database\Repository\School\CourseInformatStudent;
use Smartschool\Repository\SkoreClassTeacherCourseRelation;
use Database\Repository\School\CourseInformatEmployeeClassgroup;
use Database\Object\School\CourseInformatStudent as SchoolCourseInformatStudent;
use Database\Object\School\CourseInformatEmployeeClassgroup as SchoolCourseInformatEmployeeClassgroup;
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
        $courseIERepo = new CourseInformatEmployeeClassgroup;
        $informatStudentRepo = new Student;
        $informatEmployeeRepo = new Employee;
        $informatClassgroupRepo = new ClassGroup;
        $sourceRepo = new Source;

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering SkoreClassTeacherCourseRelations...");
        $sources = array_values(Arrays::filter($sourceRepo->get(), fn($s) => Strings::startsWith($s->id, "smartschool")));

        foreach ($sources as $source) {
            $courses = (new SkoreClassTeacherCourseRelation($source->id))->get();
            $courseISRepo->delete();
            $courseIERepo->delete();

            foreach ($courses as $course) {
                if (!$course->klasnaam || Strings::equal($course->klasnaam, 0)) continue;
                $courseName = explode(" [", $course->vaknaam)[0];

                // Save course
                $courseItem = $courseRepo->getByName($courseName) ?? (new SchoolCourse);
                $courseItem->name = $courseName;
                $nId = $courseRepo->set($courseItem);
                $courseId = $courseItem->id ?? $nId;
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, ($courseItem->id ? "INFO" : "WARN"), "Course name: {$courseName}" . ($courseItem->id ? "" : "... Created!"));

                // Link teacher and class
                if (!is_null($course->internnummer) && !is_array($course->internnummer)) {
                    $informatEmployee = $informatEmployeeRepo->getByInformatId(CString::getDigitsOnly($course->internnummer));
                    $informatClassgroup = $informatClassgroupRepo->getBySchoolyearAndCode(_CURRENT_SCHOOLYEAR_, $course->klasnaam);

                    if ($informatEmployee && $informatClassgroup) {
                        $courseInformatEmployee = $courseIERepo->getBySchoolCourseIdInformatEmployeeIdAndInformatClassgroupId($courseId, $informatEmployee->id, $informatClassgroup->id) ?? (new SchoolCourseInformatEmployeeClassgroup);
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, ($courseInformatEmployee->schoolCourseId ? "INFO" : "WARN"), "Employee Link - SIN: {$informatEmployee->informatId} ({$informatEmployee->id}); CID: {$courseId}; ICID: {$informatClassgroup->id}; STATUS: " . ($courseInformatEmployee->schoolCourseId ? "FOUND" : "CREATE"));

                        if (!$courseInformatEmployee->schoolCourseId) {
                            $courseInformatEmployee->schoolCourseId = $courseId;
                            $courseInformatEmployee->informatEmployeeId = $informatEmployee->id;
                            $courseInformatEmployee->informatClassgroupId = $informatClassgroup->id;
                            $courseIERepo->set($courseInformatEmployee);
                        }
                    }
                };

                // Link student
                foreach ($course->leerlingen['leerling'] as $student) {
                    if (is_null($student->internnummer) || is_array($student->internnummer)) continue;
                    $informatStudent = $informatStudentRepo->getByInformatId(CString::getDigitsOnly($student->internnummer));

                    if ($informatStudent) {
                        $courseInformatStudent = $courseISRepo->getBySchoolCourseIdAndInformatStudentId($courseId, $informatStudent->id) ?? (new SchoolCourseInformatStudent);
                        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, ($courseInformatStudent->schoolCourseId ? "INFO" : "WARN"), "Student Link - SIN: {$informatStudent->informatId} ({$informatStudent->id}); CID: {$courseId}; STATUS: " . ($courseInformatStudent->schoolCourseId ? "FOUND" : "CREATE"));

                        if (!$courseInformatStudent->schoolCourseId) {
                            $courseInformatStudent->schoolCourseId = $courseId;
                            $courseInformatStudent->informatStudentId = $informatStudent->id;
                            $courseISRepo->set($courseInformatStudent);
                        }
                    }
                }

                Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            }
        }

        return true;
    }
}
