<?php

namespace Security;

use Database\Repository\Setting\Setting;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
	private $receivers = [];
	private $subject = "";
	private $body = "";
	private $html = true;

	public function __construct()
	{
		$settingsRepo = new Setting;

		$this->host = $settingsRepo->get("mail.host")[0]->value;
		$this->port = $settingsRepo->get("mail.port")[0]->value;
		$this->username = $settingsRepo->get("mail.from.email")[0]->value;
		$this->password = $settingsRepo->get("mail.from.password")[0]->value;

		$this->fromEmail = $this->username;
		$this->fromName = $settingsRepo->get("mail.from.name")[0]->value;

		$this->replyEmail = $this->fromEmail;
		$this->replyName = $this->fromName;

		$this->mail = new PHPMailer(true);
	}

	public function setReceiver($mail, $name = null)
	{
		$this->receivers[] = [
			'mail' => $mail,
			'name' => $name
		];
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	public function setBody($body, $html = true)
	{
		$this->body = $body;
		$this->html = $html;
	}

	public function setReplyTo($email = null, $name = null)
	{
		$this->replyEmail = $email ?? $this->replyEmail;
		$this->replyName = $name ?? $this->replyName;
	}

	public function build()
	{
		$this->mail->SMTPDebug = SMTP::DEBUG_OFF;
		$this->mail->isSMTP();
		$this->mail->Host = $this->host;
		$this->mail->SMTPAuth = true;
		$this->mail->Username = $this->username;
		$this->mail->Password = $this->password;
		$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$this->mail->Port = $this->port;

		$this->mail->setFrom($this->fromEmail, $this->fromName);
		$this->mail->addReplyTo($this->replyEmail, $this->replyName);

		foreach ($this->receivers as $receiver) {
			$this->mail->addAddress($receiver['mail'], $receiver['name']);
		}

		$this->mail->isHTML($this->html);
		$this->mail->Subject = $this->subject;
		$this->mail->Body = $this->body;
		$this->mail->AltBody = strip_tags($this->body);
	}

	public function send()
	{
		try {
			$this->build();
			$this->mail->send();
		} catch (\Exception	$e) {
			throw $e;
		}
	}
}
