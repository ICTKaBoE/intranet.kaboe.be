<?php

use Helpers\Icon;

?>
<header class="navbar navbar-expand-md d-print-none" data-bs-theme="dark">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
			<span class="navbar-toggler-icon"></span>
		</button>
		<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
			<img src="{{site:url}}/frontend/shared/default/images/SGKaBoE blad.png" alt="" class="navbar-brand-image">
			<a href="{{site:url}}"><i class="ti ti-home"></i> {{site.title}}</a>
		</h1>
		<div class="navbar-nav flex-row order-md-last">
			<div role="notification" id="notification" data-no-notification-text="Geen updates"></div>
			<div class="nav-item dropdown">
				<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
					<span class="avatar avatar-sm">{{user:initials}}</span>
					<div class="d-none d-xl-block ps-2">
						<div>{{user:fullName}}</div>
						<div class="mt-1 small text-muted">{{user:jobTitle}} ({{user:companyName}})</div>
					</div>
				</a>
				<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
					<a href="/app/user/profile" class="dropdown-item">Profiel</a>
					<a href="/app/user/logout" class="dropdown-item">Afmelden</a>
				</div>
			</div>
		</div>
	</div>
</header>