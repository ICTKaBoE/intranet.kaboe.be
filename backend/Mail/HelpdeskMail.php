<?php

namespace Mail;

use Database\Repository\Mail;
use Security\Input;
use Security\User;

class HelpdeskMail
{
	public function sendCreationMail($ticket)
	{
		$ticket->link()->init();
		if (!Input::check($ticket->creator->username, Input::INPUT_TYPE_EMAIL)) return;

		$receivers = [[
			"email" => $ticket->creator->username,
			"name" => $ticket->creator->fullName
		]];
		$subject = "Ticket #{$ticket->number} aangemaakt!";

		$body = "
		Beste {$ticket->creator->fullName}, <br />
		<br />
		Bedankt om een ticket aan te maken.<br />
		Een medewerker zal zo snel mogelijk dit behandelen.<br />
		Om de voortgang van uw ticket te bekijken, of een reactie toe te voegen, gelieve aan te melden op <a href=\"https://intranet.kaboe.be\">Intranet</a> en ga naar <b>Helpdesk</b>.<br />
		<br />
		Het ticket heeft als nummer #{$ticket->number} en als onderwerp '{$ticket->subject}'.<br />
		<br />
		Gelieve niet te reageren op deze mail.<br />
		De mailbox wordt niet bekeken.<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers);
	}

	public function sendAssignMail($ticket)
	{
		$ticket->link()->init();
		if (!Input::check($ticket->assignedTo->username, Input::INPUT_TYPE_EMAIL)) return;

		$receivers = [
			[
				"email" => $ticket->assignedTo->username,
				"name" => $ticket->assignedTo->fullName
			]
		];
		$subject = "Ticket #{$ticket->number} toegewezen!";

		$body = "
		Beste {$ticket->assignedTo->fullName}, <br />
		<br />
		Ticket #{$ticket->number} is aan u toegewezen.<br />
		Dit ticket is aangemaakt door {$ticket->creator->fullName} en heeft als onderwerp '{$ticket->subject}'.<br />
		Om deze te behandelen, gelieve aan te melden op <a href=\"https://intranet.kaboe.be\">Intranet</a> en ga naar <b>Helpdesk</b>.<br />
		<br />
		Gelieve niet te reageren op deze mail.<br />
		De mailbox wordt niet bekeken.<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers);
	}

	public function sendUpdateMail($ticket)
	{
		$ticket->link()->init();
		if ($ticket->creatorId == User::getLoggedInUser()->id) return;
		if (!Input::check($ticket->creator->username, Input::INPUT_TYPE_EMAIL)) return;

		$receivers = [
			[
				"email" => $ticket->creator->username,
				"name" => $ticket->creator->fullName
			]
		];
		$subject = "Update voor ticket #{$ticket->number}";

		$body = "
		Beste {$ticket->creator->fullName}, <br />
		<br />
		Er is een update voor jou ticket met nummer #{$ticket->number} binnen.<br />
		Om deze te bekijken, of een reactie toe te voegen, gelieve aan te melden op <a href=\"https://intranet.kaboe.be\">Intranet</a> en ga naar <b>Helpdesk</b>.<br />
		<br />
		Gelieve niet te reageren op deze mail.<br />
		De mailbox wordt niet bekeken.<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers);
	}

	public function sendUpdateAssignedToMail($ticket)
	{
		$ticket->link()->init();
		if (!Input::check($ticket->assignedTo?->username, Input::INPUT_TYPE_EMAIL)) return;

		$receivers = [
			[
				"email" => $ticket->assignedTo->username,
				"name" => $ticket->assignedTo->fullName
			]
		];
		$subject = "Update voor toegewezen ticket #{$ticket->number}";

		$body = "
		Beste {$ticket->assignedTo->fullName}, <br />
		<br />
		Er is een update voor ticket #{$ticket->number} binnen waaraan u toegewezen bent.<br />
		Om deze te bekijken, of een reactie toe te voegen, gelieve aan te melden op <a href=\"https://intranet.kaboe.be\">Intranet</a> en ga naar <b>Helpdesk</b>.<br />
		<br />
		Gelieve niet te reageren op deze mail.<br />
		De mailbox wordt niet bekeken.<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers);
	}
}
