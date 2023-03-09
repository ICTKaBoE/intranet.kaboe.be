<div class="page-center">
	<div class="container-tight py-4 mt-5">
		<div class="text-center mb-4">
			<a href="." class="navbar-brand navbar-brand-autodark">
				<!-- <img src="./static/logo.svg" height="36" alt=""> -->
				{{site.title}}
			</a>
		</div>
		<form class="card card-md" action="{{form:action}}" method="post" autocomplete="off" id="frm{{page:id}}">
			<div class="card-body">
				<h2 class="card-title text-center mb-4">Aanmelden met je COLTD-adres</h2>
				<div class="mb-3">
					<label class="form-label" for="username">Email</label>
					<input type="email" name="username" id="username" class="form-control" placeholder="john.doe@coltd.be" required autofocus>
					<div class="invalid-feedback" data-feedback-input="username"></div>
				</div>
				<div class="mb-2">
					<label class="form-label" for="password">Wachtwoord</label>
					<input type="password" name="password" id="password" class="form-control" placeholder="Wachtwoord" required>
					<!-- <span class="input-group-text">
                            <a href="#" class="link-secondary" title="" data-bs-toggle="tooltip" data-bs-original-title="Show password">
                                Download SVG icon from http://tabler-icons.io/i/eye
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="12" cy="12" r="2"></circle>
                                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7"></path>
                                </svg>
                            </a>
                        </span> -->
					<div class="invalid-feedback" data-feedback-input="password"></div>
				</div>
				<!-- <div class="mb-2">
					<label class="form-check">
						<input type="checkbox" class="form-check-input">
						<span class="form-check-label">Onthou me in deze browser</span>
					</label>
				</div> -->
				<div class="form-footer">
					<button type="submit" class="btn btn-primary w-100 mb-2">
						Aanmelden
					</button>
					<a href="{{o365:connect}}" class="btn btn-white w-100">
						Aanmelden via Office 365
					</a>
				</div>
			</div>
			<div class="hr-text">of</div>
			<div class="card-body">
				<div class="row">
					<div class="col">
						<a href="/public" class="btn btn-white w-100">Bekijk publieke pagina's</a>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>