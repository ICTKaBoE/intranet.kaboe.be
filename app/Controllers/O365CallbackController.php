<?php

namespace Controllers;

use Security\Session;
use O365\AuthenticationManager;
use Router\Helpers;

class O365CallbackController
{
	function index()
	{
		// Get the authorization code and other parameters from the query string
		// and store them in the session.
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
			if (Helpers::url()->getParam('admin_consent', false)) Session::set('admin_consent', Helpers::url()->getParam('admin_consent', false));
			if (Helpers::url()->getParam('code', false)) Session::set('code', Helpers::url()->getParam('code', false));
			if (Helpers::url()->getParam('session_state', false)) Session::set('session_state', Helpers::url()->getParam('session_state', false));
			if (Helpers::url()->getParam('state', false)) Session::set('state', Helpers::url()->getParam('state', false));


			// With the authorization code, we can retrieve access tokens and other data.
			try {
				AuthenticationManager::acquireToken();

				Session::set(SECURITY_SESSION_ISSIGNEDIN, [
					'method' => SECURITY_SESSION_SIGNINMETHOD_O365,
					'id' => Session::get("oid")
				]);

				header('Location: ' . Helpers::url()->getScheme() . "://" . Helpers::url()->getHost());
				exit();
			} catch (\RuntimeException $e) {
				echo 'Something went wrong, couldn\'t get tokens: ' . $e->getMessage();
			}
		}
	}
}
