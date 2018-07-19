@extends('manage')

@section('title','| Manage Dashboard')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
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
						<h1 class="display-4">Welcome to {{ env('APP_NAME') }}!</h1>
					</div>
				</div>	
				<p class="lead">Thank you so much for visiting. This is my test website built with Laravel. Please read my latest post!</p>
				<a class="btn btn-primary btn-lg mt-4" href="/blog" role="button">Popular Posts</a>
			</div>
		</div>
	</div>	
@endsection

@section('scripts')
@endsection
