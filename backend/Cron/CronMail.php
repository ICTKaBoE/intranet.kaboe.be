<?php

namespace Cron;

use Database\Repository\Mail;
use Mail\Mail as MailMail;

abstract class CronMail
{
	public static function SendMail()
	{
		$mailRepo = new Mail;
		$mails = $mailRepo->getNeedToSendNow();

		foreach ($mails as $mail) {
			$mail->link();

			$_mail = new MailMail;
			$_mail->setSubject($mail->subject);
			$_mail->setBody($mail->body, $mail->html);

			foreach ($mail->receivers as $receiver) {
				$_mail->setReceiver($receiver['email'], $receiver['name']);
			}
			$_mail->setReplyTo($mail->replyTo[0]['email'] ?? null, $mail->replyTo[0]['name'] ?? null);
			$_mail->send();

			$mail->receivers = json_encode($mail->receivers);
			$mail->replyTo = json_encode($mail->replyTo);
			$mail->sent = true;
			$mailRepo->set($mail);
		}
	}
}
