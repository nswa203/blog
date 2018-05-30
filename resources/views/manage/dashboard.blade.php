@extends('manage')

@section('title','| Manage Dashboard')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<h1><a id="menu-toggle2"><span class="fas fa-cog mr-4"></span>Manage Dashboard</a></h1>
			<hr>
		</div>
	</div>	

	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h1 class="display-4">Welcome to {{ env('APP_NAME') }}!</h1>
				<p class="lead">Thank you so much for visiting. This is my test website built with Laravel. Please read my latest post!</p>
				<a class="btn btn-primary btn-lg mt-4" href="/blog" role="button">Popular Post</a>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
@endsection