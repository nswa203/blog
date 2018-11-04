@extends('manage')

@section('title','| Delete Files')

@section('stylesheets')
@endsection

@section('content')
	@if($files)
		{!! Form::open(['route'=>'files.destroy']) !!}

		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-folder-open mr-4"></span>
					@endif 				 DELETE FILES 
				</a></h1>
			</div>

			<div class="col-md-2 mt-2">
				{{ Form::button('<i class="fas fa-times-circle mr-2"></i>NO Cancel', ['class'=>'btn btn-outline-danger btn-block', 			'onclick'=>'window.history.back()']) }}
			</div>
			<div class="col-md-2 mt-2">
				{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
			</div>

			<div class="col-md-12">
				<hr>
			</div>
		</div>

		@include('partials.__filesCheck')

		{!! Form::close() !!}
	@endif
@endsection
