@extends('manage')

@section('title','| Add New Post')

@section('stylesheets')

@endsection

@section('content')
	<div class="row">
		<div class="col-md-8" >
			<h1><a id="menu-toggle2"><span class="fas fa-vial mr-4"></span>Test Area</a></h1>
			<hr>
			{!! Form::open(['route'=>['posts.search'], 'method'=>'POST']) !!}
		  	<div class="form-row align-items-center">
{{ csrf_field() }}
			    <div class="input-group">
		  			<input type="text" class="form-control" placeholder="Search Posts...">
		  			<div class="input-group-append">
		    			<button class="btn btn-outline-secondary" type="button"><span class="fas fa-search"></span></button>
		  			</div>
				</div>
	
				<div class="input-group mt-3">
					<input type="text" class="form-control" name="search" placeholder="Reset search...">
		  			<div class="input-group-append">
		    			<button class="btn btn-outline-secondary" type="submit"><span class="fas fa-sync"></span></button>
		  			</div>
				</div>

		  	</div>
			{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')

@endsection