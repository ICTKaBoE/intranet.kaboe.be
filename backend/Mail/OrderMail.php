<?php

namespace Mail;

use Database\Repository\Mail;
use Database\Repository\Module;
use Database\Repository\ModuleSetting;

class OrderMail
{
	public function sendQuotationMail($order, $orderLines)
	{
		$replyEmail = (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule('orders')->id, 'mailReplyEmail')->value;
		$replyName = (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule('orders')->id, 'mailReplyName')->value;

		$replyTo = [
			[
				"email" => $replyEmail,
				"name" => $replyName
			]
		];
		$receivers = [
			[
				"email" => $order->supplier->email,
				"name" => $order->supplier->name
			]
		];
		$subject = "Nieuwe offerte-aanvraag voor {$order->school->name}";

		$body = "
		Beste {$order->supplier->name}, <br />
		<br />
		Bij deze vragen wij een offerte voor {$order->school->name} voor onderstaande zaken.<br />
		Gelieve de offerte door te sturen in PDF bij uw antwoord.<br />
		Graag vragen wij ook de volgende referentie te gebruiken in uw offerte: {$order->number}<br />
		<br />
		Hieronder lijsten wij even op waar we een offerte voor willen verkrijgen:<br />
		<br />
		<table border='1' style='border-collapse: collapse; width: 100%'>
			<thead>
				<tr>
					<th style='text-align:left; padding: 3px 6px; width: 100px'>Aantal</th>
					<th style='text-align:left; padding: 3px 6px; width: 200px'>Voor</th>
					<th style='text-align:left; padding: 3px 6px'>Toestel</th>
					<th style='text-align:left; padding: 3px 6px'>Wat</th>
					<th style='text-align:left; padding: 3px 6px'>Reden</th>
					<th style='text-align:left; padding: 3px 6px; width: 100px'>Garantie</th>
				</tr>
			</thead>
			<tbody>";

		foreach ($orderLines as $orderLine) {
			$body .= 	"<tr>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->amount}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->forDescription}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->asset->shortDescription}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->what}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->reason}</td>
							<td style='text-align:left; padding: 3px 6px; background-color: " . ($orderLine->warenty ? 'green' : 'red') . "'>" . ($orderLine->warenty ? 'Ja' : 'Nee') . "</td>
						</tr>
			";
		}

		$body .= "</tbody>
		</table>
		<br />
		Alvast bedankt!<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers, replyTo: $replyTo);
	}

	public function sendAcceptMail($order)
	{
		$receivers = [[
			"email" => $order->acceptor->username,
			"name" => $order->acceptor->fullName
		]];
		$subject = "Nieuwe offerte ter goedkeuring: {$order->number}";

		$body = "
		Beste {$order->acceptor->fullName}, <br />
		<br />
		Bij deze vragen wij u een offerte goed te keuren voor {$order->school->name}.<br />
		Deze kan u bekijken door aan te melden op <a href='https://intranet.kaboe.be'>KaBoE Intranet</a>.<br />
		Daar kan u doorklikken op 'Bestellingen' - 'Door mij goed te keuren'.<br />
		<br />
		De offerte heeft het nummer {$order->number}.<br />
		<br />
		Alvast bedankt!<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers);
	}

	public function sendAcceptDenyMail($order)
	{
		$order->link();
		$order->init();

		$receivers = [[
			"email" => $order->creator->username,
			"name" => $order->creator->fullName
		]];
		$subject = "Offerte {$order->statusFull}: {$order->number}";

		$body = "
		Beste {$order->creator->fullName}, <br />
		<br />
		Bij deze melden wij u dat de offete met nummer {$order->number} is {$order->statusFull}.<br />
		<br />
		Alvast bedankt!<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers);
	}

	public function sendPostMail($order, $orderLines)
	{
		$replyEmail = (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule('orders')->id, 'mailReplyEmail')->value;
		$replyName = (new ModuleSetting)->getByModuleAndKey((new Module)->getByModule('orders')->id, 'mailReplyName')->value;

		$replyTo = [
			[
				"email" => $replyEmail,
				"name" => $replyName
			]
		];
		$receivers = [
			[
				"email" => $order->supplier->email,
				"name" => $order->supplier->name
			]
		];
		$subject = "Nieuwe bestelling n.a.v. offerte-aanvraag met referentie {$order->number} voor {$order->school->name}";

		$body = "
		Beste {$order->supplier->name}, <br />
		<br />
		Bij deze willen wij de offerte met referentie {$order->number} voor {$order->school->name} over laten gaan in een bestelling.<br />
		Gelieve na de bestelling de factuur door te sturen in PDF als antwoord op deze mail.<br />
		Graag vragen wij ook de volgende referentie te gebruiken in uw factuur: {$order->number}<br />
		<br />
		Hieronder lijsten wij nog even op wat er besteld mag worden:<br />
		<br />
		<table border='1' style='border-collapse: collapse; width: 100%'>
			<thead>
				<tr>
					<th style='text-align:left; padding: 3px 6px; width: 100px'>Aantal</th>
					<th style='text-align:left; padding: 3px 6px; width: 200px'>Voor</th>
					<th style='text-align:left; padding: 3px 6px'>Toestel</th>
					<th style='text-align:left; padding: 3px 6px'>Wat</th>
					<th style='text-align:left; padding: 3px 6px'>Reden</th>
					<th style='text-align:left; padding: 3px 6px; width: 100px'>Garantie</th>
				</tr>
			</thead>
			<tbody>";

		foreach ($orderLines as $orderLine) {
			$body .= 	"<tr>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->amount}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->forDescription}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->asset->shortDescription}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->what}</td>
							<td style='text-align:left; padding: 3px 6px'>{$orderLine->reason}</td>
							<td style='text-align:left; padding: 3px 6px; background-color: " . ($orderLine->warenty ? 'green' : 'red') . "'>" . ($orderLine->warenty ? 'Ja' : 'Nee') . "</td>
						</tr>
			";
		}

		$body .= "</tbody>
		</table>
		<br />
		Alvast bedankt!<br />
		<br />
		Met vriendelijke groeten,<br />
		ICT-Dienst KaBoE<br />
		";

		Mail::write($subject, $body, $receivers, replyTo: $replyTo);
	}
}
