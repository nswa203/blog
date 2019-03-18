@extends('manage')

@section('title','| Manage Dashboard')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-cog mr-4"></span>Manage Dashboard</a></h1>
			<hr>
		</div>
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-2 inline">
						<img style="margin-top:-32px; max-width:100%; max-height:128px; float:left;" src="{{ asset('favicon.ico') }}">
					</div>

					<div class="col-md-10 mb-5">
						<h1 class="display-4">Welcome to {{ config('app.name') }}!</h1>
					</div>
				</div>	
				<p class="lead">Thank you so much for visiting. This is a test website built with Laravel. Please read the most recent posts.</p>
				<p class="lead">This "Manage" area of the blog is shared between users with different permissions - some functions may not be available to you!</p>
				<a class="btn btn-primary btn-lg mt-4" href="/blog" role="button">Popular Posts</a>
			</div>
		</div>
	</div>	
@endsection

@section('scripts')
@endsection
