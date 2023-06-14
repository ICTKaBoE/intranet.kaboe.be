<?php

use Helpers\Icon;

?>
<header class="navbar navbar-expand-md d-print-none" data-bs-theme="dark">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
			<span class="navbar-toggler-icon"></span>
		</button>
		<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
			<a href="{{site:url}}"><?= Icon::load("home"); ?> {{site.title}}</a>
		</h1>
		<div class="navbar-nav flex-row order-md-last">
			<div role="notification" id="notification" data-no-notification-text="Geen updates">
				<!-- <div class="nav-item dropdown d-none d-md-flex me-3"> -->

				<!-- <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card"> -->
				<!-- <div class="card"> -->
				<!-- <div class="card-header">
					<h3 class="card-title">Geen updates</h3>
				</div> -->
				<!-- <div class="list-group list-group-flush list-group-hoverable">
					<div class="list-group-item">
						<div class="row align-items-center">
							<div class="col text-truncate">
								<a href="#" class="text-body d-block">Example 1</a>
								<div class="d-block text-muted text-truncate mt-n1">
									Change deprecated html tags to text decoration classes (#29604)
								</div>
							</div>
						</div>
					</div>
				</div> -->
				<!-- </div> -->
				<!-- </div> -->
				<!-- </div> -->
			</div>
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