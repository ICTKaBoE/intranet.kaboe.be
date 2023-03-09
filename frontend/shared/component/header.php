<?php

use Helpers\Icon;

?>
<header class="navbar navbar-expand-md navbar-dark d-print-none">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
			<span class="navbar-toggler-icon"></span>
		</button>
		<h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
			<a href="{{site:url}}"><?= Icon::load("home"); ?> {{site.title}}</a>
		</h1>
		<div class="navbar-nav flex-row order-md-last">
			<div class="d-none d-md-flex">
				<div class="nav-item dropdown d-none d-md-flex me-3">
					<a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
						<?= Icon::load("bell"); ?>
						<!-- <span class="badge bg-red"></span> -->
					</a>
					<div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
						<div class="card">
							<div class="card-header">
								<h3 class="card-title">Geen updates</h3>
							</div>
							<!-- <div class="list-group list-group-flush list-group-hoverable">
								<div class="list-group-item">
									<div class="row align-items-center">
										<div class="col-auto"><span class="status-dot status-dot-animated bg-red d-block"></span></div>
										<div class="col text-truncate">
											<a href="#" class="text-body d-block">Example 1</a>
											<div class="d-block text-muted text-truncate mt-n1">
												Change deprecated html tags to text decoration classes (#29604)
											</div>
										</div>
										<div class="col-auto">
											<a href="#" class="list-group-item-actions">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon text-muted" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
													<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
													<path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"></path>
												</svg>
											</a>
										</div>
									</div>
								</div>
							</div> -->
						</div>
					</div>
				</div>
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
					<div class="dropdown-divider"></div>
					<a href="/app/user/logout" class="dropdown-item">Afmelden</a>
				</div>
			</div>
		</div>
	</div>
</header>