@extends('main')

@section('title','| Homepage')

@section('content')
	<div class="row mt-5">
		<div class="col-md-12">
			<div class="jumbotron">
				<h1 class="display-4">Welcome to My Blog!</h1>
				<p class="lead">Thank you so much for visiting. This is my test website built with Laravel. Please read my latest post!</p>
				<a class="btn btn-primary btn-lg mt-4" href="#" role="button">Popular Post</a>
			</div>
		</div>
	</div>
	
	<div class="row mt-5">
		<div class="col-md-8">
			<div class="post">
				<h3>Post Title</h3>
				<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to...</p>
				<a href="#" class="btn btn-primary">Read More</a>
			</div>
			<hr>
			<div class="post">
				<h3>Post Title</h3>
				<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable...</p>
				<a href="#" class="btn btn-primary">Read More</a>
			</div>
			<hr>
			<div class="post">
				<h3>Post Title</h3>
				<p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure...</p>
				<a href="#" class="btn btn-primary">Read More</a>
			</div>
			<hr>
			<div class="post">
				<h3>Post Title</h3>
				<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure ...</p>
				<a href="#" class="btn btn-primary">Read More</a>
			</div>
		</div>	
		<div class="col-md-3 offset-md-1">
			<h2>Sidebar</h2>
			<p>Just some text to show the extent of the sidebar.</p>
		</div>
	</div>
@endsection
