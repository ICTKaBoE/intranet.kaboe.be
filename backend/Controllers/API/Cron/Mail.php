<?php

namespace Controllers\API\Cron;

use Helpers\Log;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Mail as SecurityMail;
use Database\Repository\Mail\Receiver;
use Database\Repository\Mail\Mail as MailMail;

abstract class Mail
{
    public static function Send()
    {
        define("_LOGTIMESTAMP_", Clock::nowAsString("Y-m-d H-i-s"));
        define("_LOGLOCATION_", "cron/mail");
        Log::Open(_LOGLOCATION_, _LOGTIMESTAMP_);

        $return = true;
        $repo = new MailMail;
        $recRepo = new Receiver;

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering mails to be sent...");
        $mailsToBeSend = $repo->get();
        $mailsToBeSend = Arrays::filter($mailsToBeSend, fn($m) => Strings::isBlank($m->sentDateTime));
        $mailsToBeSend = Arrays::filter($mailsToBeSend, fn($m) => Clock::now()->isAfterOrEqualTo(Clock::at($m->sendAfterDateTim)));
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", count($mailsToBeSend) . " mails to be sent!");

        foreach ($mailsToBeSend as $mail) {
            $receivers = $recRepo->getByMailId($mail->id);
            $m = new SecurityMail;

            $m->setSubject($mail->subject);
            $m->setBody($mail->body);
            $m->setReplyTo($mail->replyTo['email'], $mail->replyTo['name']);

            foreach ($receivers as $r) {
                $m->setReceiver($r->email, $r->name);
            }

            try {
                $m->send();
                $mail->sentDateTime = Clock::nowAsString("Y-m-d H:i:s");
                $mail->error = null;
                $repo->set($mail);
            } catch (\Exception $e) {
                $mail->error = $e->getMessage();
                $repo->set($mail);
                $return = false;
            }
        }

        Log::Close(_LOGLOCATION_, _LOGTIMESTAMP_);

        return $return;
    }
}
