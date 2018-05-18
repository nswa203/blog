@extends('main')

@section('title','| Contact')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h1><span class="fas fa-envelope mr-4"></span>Contact Me</h1>
				<hr>
				<form action="{{ url('contact') }}" method="POST">
					{{ csrf_field() }}
					<div class="form-group">
						<label name="name" class="font-bold">Your Name:</label>
						<input id="name" name="name" class="form-control">
					</div>
					<div class="form-group">
						<label name="email" class="font-bold">Your eMail:</label>
						<input id="email" name="email" class="form-control">
					</div>
					<div class="form-group">
						<label name="email" class="font-bold">Subject:</label>
						<input id="subject" name="subject" class="form-control">
					</div>
					<div class="form-group">
						<label name="message" class="font-bold">Message:</label>
						<textarea id="message" name="message" class="form-control" placeholder="Type your message here..."></textarea>
					</div>
					<input type="submit" value="Send Message" class="btn btn-success">
				</form>
			</div>
		</div>
	</div>
@endsection