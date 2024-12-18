<?php

namespace Controllers\API\Cron;

use Database\Repository\Mail\Mail as MailMail;
use Database\Repository\Mail\Receiver;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;
use Security\Mail as SecurityMail;

abstract class Mail
{
    public static function Send()
    {
        $return = true;
        $repo = new MailMail;
        $recRepo = new Receiver;
        $mailsToBeSend = $repo->get();
        $mailsToBeSend = Arrays::filter($mailsToBeSend, fn($m) => Strings::isBlank($m->sentDateTime));
        $mailsToBeSend = Arrays::filter($mailsToBeSend, fn($m) => Clock::now()->isAfterOrEqualTo(Clock::at($m->sendAfterDateTim)));

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

        return $return;
    }
}
