@extends('manage')

@section('title','| Test Search')

@section('stylesheets')

@endsection

@section('content')
	<div class="row">
		<div class="col-md-8" >
			<h1><a id="menu-toggle2"><span class="fas fa-vial mr-4"></span>Test Search</a></h1>
			<hr>

			{{Session::get('zone')}}

			{!! Form::open(['route'=>['search.index'], 'method'=>'POST']) !!}
				<div class="input-group">
					{{ Form::text('search', null,  ['class'=>'form-control', 'placeholder'=>'Search '.Session::get('zone').'...']) }} 
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="submit"><span class="fas fa-search"></span></button>
					</div>
				</div>
			{!! Form::close() !!}





			{!! Form::open(['route'=>['search.index'], 'method'=>'POST']) !!}
		  	<div class="form-row align-items-center mt-4">
				{{ csrf_field() }}
			    <div class="input-group">
		  			<input type="text" class="form-control" name="search" placeholder="Search {{ Session::get('zone') }}...">
		  			<div class="input-group-append">
		    			<button class="btn btn-outline-secondary" type="submit"><span class="fas fa-search"></span></button>
		    			<button class="btn btn-outline-secondary" type="button"><span class="fas fa-sync"></span></button>
		  			</div>
				</div>
	
				<div class="input-group mt-3">
					<input type="text" class="form-control" name="" placeholder="Reset search...">
		  			<div class="input-group-append">
		    			<button class="btn btn-outline-secondary" type="button"><span class="fas fa-sync"></span></button>
		  			</div>
				</div>

		  	</div>
			{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')

@endsection