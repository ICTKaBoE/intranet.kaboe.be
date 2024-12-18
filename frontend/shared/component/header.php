<?php if ($_COOKIE["component_header_mode"] == "minimal"): ?>
	<div class="sticky-top">
		<header class="navbar navbar-expand-md sticky-top d-print-none">
			<div class="container-fluid">
				<h1 class="navbar-brand navbar-brand-autodark">
					<a href="{{site:url}}">
						<img src="{{site:url}}/frontend/shared/default/images/SGKaBoE blad.png" alt="" class="navbar-brand-image">
						<span class="ms-2">{{setting:site.title.intranet}}</span>
					</a>
				</h1>

				<div class="col-auto">
					<div class="navbar-nav">
						<div class="nav-item dropdown">
							<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
								<span class="avatar avatar-sm">{{user:initials}}</span>
								<div class="d-none d-xl-block ps-2">
									<div>{{user:fullName}}</div>
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
	</div>
<?php else: ?>
	<header class="navbar navbar-expand-md d-print-none">
		<div class="container-fluid">
			<div class="col">
				<div class="row">
					<div class="col-auto">{{component:pagetitle?mode=intranet}}</div>
					<div class="col col-lg-auto d-flex justify-content-end text-right">{{component:actionButtons}}</div>
					<div class="d-none d-lg-flex col-auto">{{component:searchField}}</div>
					<div class="d-none d-lg-flex col-auto">{{component:extraPageInfo}}</div>
				</div>
			</div>

			<div class="col-auto d-none d-lg-flex">
				<div class="navbar-nav">
					<div class="nav-item dropdown">
						<a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
							<span class="avatar avatar-sm">{{user:initials}}</span>
							<div class="d-none d-xl-block ps-2">
								<div>{{user:fullName}}</div>
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
<?php endif; ?>