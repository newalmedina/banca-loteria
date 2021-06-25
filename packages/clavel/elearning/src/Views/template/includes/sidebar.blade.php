<div class="header-container container custom-position-initial">
	<div class="header-row">
		<div class="header-column">
			<div class="header-logo">
                <div>{{ trans("elearning::general/front_lang.organizado") }}</div>
				<a href="{{ url("/") }}">
					<img alt="{{ env("PROJECT_NAME") }}" src="{{ asset("assets/front/img/logo.svg") }}">
				</a>
			</div>
		</div>
		<div class="header-column">
			<div class="header-row">
				<div class="header-nav">
					<button class="btn header-btn-collapse-nav" data-toggle="collapse" data-target=".header-nav-main">
						<i class="fa fa-bars" aria-hidden="true"></i>
					</button>
					<div class="header-nav-main header-nav-main-effect-1 header-nav-main-sub-effect-1 collapse m-none">
							<nav class="nav navbar-nav">
								{!! CustomMenu::render('navbar') !!}
							</nav>
							@include('front.includes.notifications')
							<nav class="nav navbar-nav navbar-right">
								{!! CustomMenu::render('navbar-right') !!}
							</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
