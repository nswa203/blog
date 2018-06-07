@extends('manage')

@section('title','| Add New Post')

@section('stylesheets')

@endsection

@section('content')
	<div class="row">
		<div class="col-md-8" >
			<h1><a id="menu-toggle2"><span class="fas fa-vial mr-4"></span>Test Area</a></h1>
			<hr>

			<form>
			  	<div class="form-row align-items-center">
				    <div class="input-group">
			  			<input type="text" class="form-control" placeholder="Search Posts...">
			  			<div class="input-group-append">
			    			<button class="btn btn-outline-secondary" type="button"><span class="fas fa-search"></span></button>
			  			</div>
					</div>
		
				    <div class="input-group mt-3">
						<input type="text" class="form-control" placeholder="Reset search...">
			  			<div class="input-group-append">
			    			<button class="btn btn-outline-secondary" type="button"><span class="fas fa-sync"></span></button>
			  			</div>
					</div>
		
			  	</div>
			</form>
			
		</div>
	</div>
@endsection

@section('scripts')

@endsection