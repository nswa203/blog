<nav class="navbar navbar-expand-lg navbar-light bg-light">
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
						<a class="dropdown-item" href="{{ route('posts.index'		) }}"><span class="fas fa-file-alt mr-2"></span>Posts</a>
						<a class="dropdown-item" href="{{ route('categories.index'	) }}"><span class="fas fa-tasks mr-2"></span>Categories</a>
						<a class="dropdown-item" href="{{ route('tags.index'		) }}"><span class="fas fa-tag mr-2"></span>Tags</a>
						<div class="dropdown-divider"></div>
						{!! Form::open(['route'=>['logout'],'method'=>'POST']) !!}
							{!! Form::submit('Log Out',['class'=>'dropdown-item']) !!}
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
