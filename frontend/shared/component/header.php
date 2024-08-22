<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="navbar-nav ms-auto">
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