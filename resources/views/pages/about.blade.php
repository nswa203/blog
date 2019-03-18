@extends('main')

@section('title','| About')

@section('content')
	<div class="row">
		<div class="col-md-12 myWrap">
			<div class="jumbotron">
				<h1 class="mb-5"><span class="fas fa-info-circle mr-4"></span>About {{ $data['name'] }}</h1>
				<p>
					This site is a blog designed for registered users to create postings for a public readership. Anyone may read published postings and add comments.
				</p>
				<p>
					Postings may contain text and embedded images as well as links to albums of uploaded images and links to folders of uploaded files.
				</p>
				<p>
					Anyone is free to register as a Subscriber. Contact <a href="/contact">The Administrators</a> if you wish to apply for additional privileges.  
				</p>
				<p>
					This site is developed with Laravel 5+ and is running on XAMPP Apache + MariaDB + PHP + Perl.
					Includes packages: Laratrust, Vue 2, Parsley, Select 2, TinyMCE, jsMediaTags, Intervention, PHPCoord, Ordnance Survey OST50.
				</p>
				<p>	
					Client browsers require JavaScript enabled for full functionality. 
				</p>				
				<p>
					<a href="/contact">The Administrators</a> and their designated Moderators reserve the right to remove or edit any postings.  
				</p>
			</div>
		</div>
	</div>
@endsection