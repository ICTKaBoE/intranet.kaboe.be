<header class="navbar navbar-expand-md d-print-none">
	<div class="container-fluid">
		<div class="col">
			<div class="row">
				<div class="d-none d-lg-flex col-2">{{component:searchField}}</div>
				<div class="col">{{component:actionButtons}}</div>
			</div>
		</div>

		<div class="col-auto d-none d-lg-flex">
			<div class="navbar-nav">
				<div class="nav-item dropdown">
					<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
						<span class="avatar avatar-sm">{{user:initials}}</span>
						<div class="d-none d-xl-block ps-2">
							<div>{{user:fullName}}</div>
							<div class="mt-1 small text-muted">{{user:jobTitle}} ({{user:companyName}})</div>
						</div>
					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
						<a href="/user/profile" class="dropdown-item">Profiel</a>
						<a href="/user/logout" class="dropdown-item">Afmelden</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>