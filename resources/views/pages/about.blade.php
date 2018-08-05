@extends('main')

@section('title','| About')

@section('content')
	<div class="row">
		<div class="col-md-12 myWrap">
			<div class="jumbotron">
				<h1 class="mb-5"><span class="fas fa-info-circle mr-4"></span>About {{ env('APP_NAME') }}</h1>
				<p>
					This site is a blog designed for registered users to create postings for a public readership. Anyone may read published postings and add comments.
				</p>
				<p>
					Postings may contain text and embedded images as well as links to albums of uploaded images and links to folders of uploaded files.
				</p>
				<p>
					Anyone is free to register as a Subscriber or Contributor. Contact <a href="/contact">The Administrators</a> if you wish to apply for additional privileges.  
				</p>
				<p>
					<a href="/contact">The Administrators</a> and their designated Editors reserve the right to remove or edit any postings.  
				</p>
			</div>
		</div>
	</div>
@endsection