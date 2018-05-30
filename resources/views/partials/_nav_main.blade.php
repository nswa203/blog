<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
	<a class="navbar-brand" href="/">{{ env('APP_NAME') }}</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item {{ Request::is('/'			)?'active':'' }}"><a class="nav-link" href="/"			>Home</a></li>
			<li class="nav-item {{ Request::is('blog'		)?'active':'' }}"><a class="nav-link" href="/blog"		>Blog</a></li>
			<li class="nav-item {{ Request::is('about'		)?'active':'' }}"><a class="nav-link" href="/about"		>About</a></li>
			<li class="nav-item {{ Request::is('contact'	)?'active':'' }}"><a class="nav-link" href="/contact"	>Contact</a></li>
		</ul>

		<ul class="navbar-nav navbar-right">
			@if (Auth::check())
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Hello {{ Auth::user()->name }}
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
						<a class="dropdown-item" href="{{ route('manage.dashboard'	) }}"><span class="fas fa-cog	 		mr-2"></span>Manage</a>
						<a class="dropdown-item" href="{{ route('posts.index'		) }}"><span class="fas fa-file-alt		mr-2"></span>Posts</a>
						<a class="dropdown-item" href="{{ route('comments.index'	) }}"><span class="fas fa-comment-alt	mr-2"></span>Comments</a>
						<a class="dropdown-item" href="{{ route('tags.index'		) }}"><span class="fas fa-tag			mr-2"></span>Tags</a>
						<a class="dropdown-item" href="{{ route('categories.index'	) }}"><span class="fas fa-list-alt		mr-2"></span>Categories</a>
							<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="{{ route('tests.show','1'   	) }}"><span class="fas fa-vial			mr-2"></span>Tests Show</a>
						<a class="dropdown-item" href="{{ route('tests.create'	 	) }}"><span class="fas fa-vial			mr-2"></span>Tests Create</a>
							<div class="dropdown-divider"></div>
						{!! Form::open(['route'=>['logout'],'method'=>'POST']) !!}
						{!! Form::button('<span class="fas fa-sign-out-alt mr-2"></span>Log Out', ['type'=>'submit', 'class'=>'dropdown-item'] ) !!}
						{!! Form::close() !!}
					</div>
				</li>
			@else
				<li class="nav-item {{ Request::is('register'	)?'active':'' }}"><a class="nav-link btn" href="{{ route('register'	) }}">Register</a></li>
				<li class="nav-item {{ Request::is('login'		)?'active':'' }}"><a class="nav-link btn" href="{{ route('login'	) }}">Login</a></li>
			@endif
		</ul>

	</div>
</nav>
