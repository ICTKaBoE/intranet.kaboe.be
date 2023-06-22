<?php

namespace Mail;

use Database\Repository\Setting;
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

		$this->host = $settingsRepo->get("mailer.host")[0]->value;
		$this->port = $settingsRepo->get("mailer.port")[0]->value;
		$this->username = $settingsRepo->get("mailer.from.email")[0]->value;
		$this->password = $settingsRepo->get("mailer.from.password")[0]->value;

		$this->fromName = $settingsRepo->get("mailer.from.name")[0]->value;
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

		$this->mail->setFrom($this->username, $this->fromName);

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
