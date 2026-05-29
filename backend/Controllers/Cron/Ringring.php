<?php

namespace Controllers\Cron;

use Database\Repository\COLTDAlert\COLTDAlertMessage;
use Database\Repository\Setting\Setting;
use Helpers\Log;
use RingRing\Client;
use RingRing\Model\Request\MessageStatusRequest;

abstract class Ringring
{
    static public function Import()
    {
        $repo = new COLTDAlertMessage;
        $client = new Client((new Setting)->getById("ringring.key")->value);

        foreach ($repo->get() as $message) {
            Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Checking status of message {$message->ringringGuid}");
            $result = $client->getMessageStatus(new MessageStatusRequest(['messageID' => $message->ringringGuid]));
            $result = json_decode($result, true);
            $message->statusCode = $result['StatusCode'];
            $message->statusDescription = $result['StatusDescription'];
            $message->timeScheduled = $result['TimeScheduled'];
            $message->timeDelivered = $result['DeliveryTime'];
            $repo->set($message);
        }

        return true;
    }
}
