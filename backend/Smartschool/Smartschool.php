<?php

namespace Smartschool;

abstract class Smartschool
{
    static public function GetErrorCodes($sourceId)
    {
        $connection = Connection::init($sourceId);

        return json_decode($connection->returnJsonErrorCodes(), true);
    }

    static public function SendMessage($sourceId, $receiver, $subject, $body, $attachments = [], $account = 0, $copyToLvs = false)
    {
        $connection = Connection::init($sourceId);
        $connectionPassword = Connection::GetPassword($sourceId);

        $connection->sendMsg($connectionPassword, $receiver, $subject, $body, "noreply", json_encode($attachments), $account, $copyToLvs);
    }

    static public function SaveUserBasic($sourceId, $internalNumber, $username, $password, $name, $surname, $email, $role)
    {
        $connection = Connection::init($sourceId);
        $connectionPassword = Connection::GetPassword($sourceId);

        return $connection->saveUser(
            $connectionPassword,
            $internalNumber,
            $username,
            $password,
            null,
            null,
            $name,
            $surname,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $email,
            null,
            null,
            null,
            null,
            null,
            $role,
            null
        );
    }

    static public function AddUserToGroup($sourceId, $username, $groupCode)
    {
        $connection = Connection::init($sourceId);
        $connectionPassword = Connection::GetPassword($sourceId);

        return $connection->saveUserToClass($connectionPassword, $username, $groupCode, null);
    }

    static public function RemoveUserFromGroup($sourceId, $username, $groupCode)
    {
        $connection = Connection::init($sourceId);
        $connectionPassword = Connection::GetPassword($sourceId);

        return $connection->removeUserFromGroup($connectionPassword, $username, $groupCode);
    }
}
