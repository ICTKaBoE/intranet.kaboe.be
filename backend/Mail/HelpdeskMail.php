<?php

namespace Mail;

class HelpdeskMail extends Mail
{
	public function sendCreationMail($email, $name, $ticketNumber)
	{
		$this->setReceiver($email, $name);
		$this->setSubject("Ticket #{$ticketNumber} aangemaakt!");

		$body = "
		Beste {$name}, <br />
		<br />
		Bedankt om een ticket aan te maken.<br />
		Een medewerker zal zo snel mogelijk dit behandelen.<br />
		Om de voortgang van uw ticket te bekijken, of een reactie toe te voegen, gelieve aan te melden op <a href=\"https://intranet.kaboe.be\">Intranet</a> en ga naar <b>Helpdesk</b>.<br />
		<br />
		Gelieve niet te reageren op deze mail.<br />
		De mailbox wordt niet bekeken.<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		$this->setBody($body);
		$this->build();
		$this->send();
	}

	public function sendUpdateMail($email, $name, $ticketNumber)
	{
		$this->setReceiver($email, $name);
		$this->setSubject("Update voor ticket #{$ticketNumber}");

		$body = "
		Beste {$name}, <br />
		<br />
		Er is een update voor jou ticket binnen.<br />
		Om deze te bekijken, of een reactie toe te voegen, gelieve aan te melden op <a href=\"https://intranet.kaboe.be\">Intranet</a> en ga naar <b>Helpdesk</b>.<br />
		<br />
		Gelieve niet te reageren op deze mail.<br />
		De mailbox wordt niet bekeken.<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		$this->setBody($body);
		$this->build();
		$this->send();
	}
}