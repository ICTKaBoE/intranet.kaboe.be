<?php

namespace Controllers\Cron;

use Database\Repository\School\School;
use Database\Repository\Source;
use GuzzleHttp\Client;
use Helpers\CString;
use Helpers\Log;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Smartschool\Repository\AllAccountsExtended;

abstract class Eetjemee
{
    static public function Import()
    {
        $status[] = self::ImportEmployee();
        Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        $status[] = self::ImportStudent();
        return !Arrays::contains($status, false);
    }

    static private function ImportStudent()
    {
        $schoolRepo = new School;

        $schools = Arrays::filter($schoolRepo->get(), fn($i) => !is_null(Strings::trimToNull($i->eetjemeeKeyStudents)) && !is_null(Strings::trimToNull($i->smsGroupStudents)));
        $client = new Client(['base_uri' => "https://api.eetjemee.be"]);

        foreach ($schools as $school) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "School: {$school->name}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering AllAccountsExtended in group '{$school->smsGroupStudents}'...");
            $aaeRepo = new AllAccountsExtended($school->linked->parentSchool->smartschoolSourceId ?: $school->smartschoolSourceId);
            $students = $aaeRepo->get($school->smsGroupStudents, '1');
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Total: " . count($students));

            $upsert = Arrays::filter($students, fn($s) => Strings::equal($s->status, "actief"));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Upsert: " . count($upsert));

            $archive = Arrays::filter($students, fn($s) => $s->schoolverlater || !Strings::equal($s->status, "actief"));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archive: " . count($archive));

            Arrays::each($upsert, fn($u) => $u->officialClass = Arrays::firstOrNull(Arrays::filter($u->groups, fn($uoc) => $uoc->isKlas && $uoc->isOfficial))->name);
            $upsert = Arrays::filter($upsert, fn($u) => !is_null($u->officialClass) || !is_null($u->Cateringtarief));

            $upsert = Arrays::map($upsert, fn($u) => [
                'child_firstname' => $u->voornaam,
                'child_lastname' => $u->naam,
                'child_dateofbirth' => $u->geboortedatum,
                'child_regnr' => CString::getDigitsOnly($u->rijksregisternummer),
                'fk_external_ref' => $u->internnummer,
                'external_barcode' => $u->BadgeID ?: null,
                'child_goout' => 0,
                'class_name' => $u->Cateringtarief ?: $u->officialClass
            ]);

            $archive = Arrays::map($archive, fn($a) => [
                'child_regnr' => CString::getDigitsOnly($a->rijksregisternummer),
                'fk_external_ref' => $a->internnummer,
                'external_barcode' => $a->BadgeID ?: null
            ]);

            $upsert = array_values($upsert);
            $archive = array_values($archive);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Upserting...");
            $upsertResponse = json_decode($client->post(
                "/v1/students/upsert-batch",
                [
                    "body" => json_encode(['students' => $upsert]),
                    "headers" => ["Authorization" => "Bearer {$school->eetjemeeKeyStudents}"]
                ]
            )->getBody()->getContents(), true);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "OK: {$upsertResponse['ok']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Timing: {$upsertResponse['timing_ms']}ms");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Received: {$upsertResponse['received']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Processed: {$upsertResponse['processed']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Skipped: {$upsertResponse['skipped']}");

            if (!empty($upsertResponse['errors'])) {
                foreach ($upsertResponse['errors'] as $error) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "{$error['code']}: {$error['value']}");
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archiving...");

            $archiveResponse = json_decode($client->post(
                "/v1/students/archive-batch",
                [
                    "body" => json_encode(['students' => $archive]),
                    "headers" => ["Authorization" => "Bearer {$school->eetjemeeKeyStudents}"]
                ]
            )->getBody()->getContents(), true);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "OK: {$archiveResponse['ok']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Timing: {$archiveResponse['timing_ms']}ms");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Received: {$archiveResponse['received']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archived: {$archiveResponse['archived']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Not found: {$archiveResponse['not_found']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Skipped: {$archiveResponse['skipped']}");

            if (!empty($archiveResponse['errors'])) {
                foreach ($archiveResponse['errors'] as $error) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "{$error['code']}: {$error['value']}");
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return true;
    }

    static private function ImportEmployee()
    {
        $schoolRepo = new School;

        $schools = Arrays::filter($schoolRepo->get(), fn($i) => !is_null(Strings::trimToNull($i->eetjemeeKeyEmployee)) && !is_null(Strings::trimToNull($i->smsGroupEmployee)));
        $client = new Client(['base_uri' => "https://api.eetjemee.be"]);

        foreach ($schools as $school) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "School: {$school->name}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering AllAccountsExtended in group '{$school->smsGroupEmployee}'...");
            $aaeRepo = new AllAccountsExtended($school->linked->parentSchool->smartschoolSourceId ?: $school->smartschoolSourceId);
            $employees = $aaeRepo->get($school->smsGroupEmployee, '1');
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Total: " . count($employees));

            $upsert = Arrays::filter($employees, fn($s) => Strings::equal($s->status, "actief"));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Upsert: " . count($upsert));

            $archive = Arrays::filter($employees, fn($s) => !Strings::equal($s->status, "actief"));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archive: " . count($archive));

            $upsert = Arrays::map($upsert, fn($u) => [
                'teacher_firstname' => $u->voornaam,
                'teacher_lastname' => $u->naam,
                'teacher_email' => $u->gebruikersnaam,
                'teacher_regnr' => CString::getDigitsOnly($u->rijksregisternummer),
                'fk_external_ref' => $u->internnummer,
                'external_barcode' => $u->BadgeID ?: null,
            ]);

            $archive = Arrays::map($archive, fn($a) => [
                'teacher_regnr' => CString::getDigitsOnly($a->rijksregisternummer),
                'fk_external_ref' => $a->internnummer,
                'external_barcode' => $a->BadgeID ?: null
            ]);

            $upsert = array_values($upsert);
            $archive = array_values($archive);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Upserting...");
            $upsertResponse = json_decode($client->post(
                "/v1/teachers/upsert-batch",
                [
                    "body" => json_encode(['teachers' => $upsert]),
                    "headers" => ["Authorization" => "Bearer {$school->eetjemeeKeyEmployee}"]
                ]
            )->getBody()->getContents(), true);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "OK: {$upsertResponse['ok']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Timing: {$upsertResponse['timing_ms']}ms");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Received: {$upsertResponse['received']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Processed: {$upsertResponse['processed']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Skipped: {$upsertResponse['skipped']}");

            if (!empty($upsertResponse['errors'])) {
                foreach ($upsertResponse['errors'] as $error) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "{$error['code']}: {$error['value']}");
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archiving...");

            $archiveResponse = json_decode($client->post(
                "/v1/teachers/archive-batch",
                [
                    "body" => json_encode(['teachers' => $archive]),
                    "headers" => ["Authorization" => "Bearer {$school->eetjemeeKeyEmployee}"]
                ]
            )->getBody()->getContents(), true);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "OK: {$archiveResponse['ok']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Timing: {$archiveResponse['timing_ms']}ms");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Received: {$archiveResponse['received']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archived: {$archiveResponse['archived']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Not found: {$archiveResponse['not_found']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Skipped: {$archiveResponse['skipped']}");

            if (!empty($archiveResponse['errors'])) {
                foreach ($archiveResponse['errors'] as $error) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "{$error['code']}: {$error['value']}");
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return true;
    }
}
