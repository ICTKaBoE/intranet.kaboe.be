<?php

namespace Smartschool;

abstract class Smartschool
{
    static public function SendMessage($sourceId, $receiver, $subject, $body, $attachments = [], $account = 0, $copyToLvs = false)
    {
        $connection = Connection::init($sourceId);
        $password = Connection::GetPassword($sourceId);

        $connection->sendMsg($password, $receiver, $subject, $body, "noreply", json_encode($attachments), $account, $copyToLvs);
    }
}
