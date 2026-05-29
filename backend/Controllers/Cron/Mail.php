<?php

namespace Controllers\Cron;

use Database\Repository\Mail\Attachment;
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
        $return = true;
        $repo = new MailMail;
        $recRepo = new Receiver;
        $attRepo = new Attachment;

        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "WARN", "Gathering mails to be sent...");
        $mailsToBeSend = $repo->get();
        $mailsToBeSend = Arrays::filter($mailsToBeSend, fn($m) => Strings::isBlank($m->sentDateTime));
        $mailsToBeSend = Arrays::filter($mailsToBeSend, fn($m) => Clock::now()->isAfterOrEqualTo(Clock::at($m->sendAfterDateTime)));
        Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", count($mailsToBeSend) . " mails to be sent!");

        foreach ($mailsToBeSend as $mail) {
            $receivers = $recRepo->getByMailId($mail->id);
            $attachments = $attRepo->getByMailId($mail->id);
            $m = new SecurityMail;

            if ($mail->fromEmail) $m->setSender($mail->fromEmail, $mail->fromName);
            $m->setSubject($mail->subject);
            $m->setBody($mail->body);
            $m->setReplyTo($mail->replyTo['email'], $mail->replyTo['name']);

            foreach ($receivers as $r) $m->setReceiver($r->email, $r->name);
            foreach ($attachments as $a) $m->setAttachment($a->path, $a->name);

            try {
                Log::Write(_LOGLOCATION_, _LOGTIMESTAMP_, "INFO", "Sending mail to " . count($receivers) . " receiver(s): " . implode(", ", Arrays::map($receivers, fn($r) => $r->email)));
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

        return $return;
    }
}
