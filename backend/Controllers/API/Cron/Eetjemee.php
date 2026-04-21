<?php

namespace Controllers\API\Cron;

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
        $schoolRepo = new School;

        $schools = Arrays::filter($schoolRepo->get(), fn($i) => !is_null(Strings::trimToNull($i->eetjemeeKey)));
        $client = new Client(['base_uri' => "https://api.eetjemee.be"]);

        foreach ($schools as $school) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "School: {$school->name}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering AllAccountsExtended...");
            $aaeRepo = new AllAccountsExtended($school->linked->parentSchool->smartschoolSourceId ?: $school->smartschoolSourceId);
            $students = $aaeRepo->get($school->eetjemeeSmartschoolGroup, '1');
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Total: " . count($students));

            $upsert = Arrays::filter($students, fn($s) => !$s->schoolverlater && !is_null($s->rijksregisternummer) && !is_null($s->internnummer) && !is_null($s->BadgeID) && !is_null($s->Cateringtarief));
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Upsert: " . count($upsert));

            $archive = Arrays::filter($students, fn($s) => $s->schoolverlater);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archive: " . count($archive));
            $upsert = array_slice($upsert, 0, 5, true);
            $archive = array_slice($archive, 0, 5, true);

            $upsert = Arrays::map($upsert, fn($u) => [
                'child_firstname' => $u->voornaam,
                'child_lastname' => $u->naam,
                'child_dateofbirth' => $u->geboortedatum,
                'child_regnr' => CString::getDigitsOnly($u->rijksregisternummer),
                'fk_external_ref' => $u->internnummer,
                'external_barcode' => $u->BadgeID,
                'child_goout' => 0,
                'class_name' => $u->Cateringtarief
            ]);

            $archive = Arrays::map($archive, fn($a) => [
                'child_regnr' => CString::getDigitsOnly($a->rijksregisternummer),
                'fk_external_ref' => $a->internnummer,
                'external_barcode' => $a->BadgeID
            ]);

            $upsert = array_values($upsert);
            $archive = array_values($archive);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Upserting...");
            $upsertResponse = json_decode($client->post(
                "/v1/students/upsert-batch",
                [
                    "body" => json_encode(['students' => $upsert]),
                    "headers" => ["Authorization" => "Bearer {$school->eetjemeeKey}"]
                ]
            )->getBody()->getContents(), true);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "OK: {$upsertResponse['ok']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Timing: {$upsertResponse['timing_ms']}ms");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Received: {$upsertResponse['received']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Processed: {$upsertResponse['processed']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Skipped: {$upsertResponse['skipped']}");

            if (!empty($upsertResponse['errors'])) {
                foreach ($upsertResponse['errors'] as $error) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "{$error['fk_external_ref']} ({$error['child_regnr']}): {$error['code']}");
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archiving...");

            $archiveResponse = json_decode($client->post(
                "/v1/students/archive-batch",
                [
                    "body" => json_encode(['students' => $archive]),
                    "headers" => ["Authorization" => "Bearer {$school->eetjemeeKey}"]
                ]
            )->getBody()->getContents(), true);

            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "OK: {$archiveResponse['ok']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Timing: {$archiveResponse['timing_ms']}ms");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Received: {$archiveResponse['received']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Archived: {$archiveResponse['archived']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Not found: {$archiveResponse['not_found']}");
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Skipped: {$archiveResponse['skipped']}");

            if (!empty($archiveResponse['errors'])) {
                foreach ($archiveResponse['errors'] as $error) Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "ERROR", "{$error['fk_external_ref']} ({$error['child_regnr']}): {$error['code']}");
            }

            Log::EmptyLine(_LOGLOCATION_, _LOGTIMESTAMP_);
        }

        return true;
    }
}
