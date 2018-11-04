@extends('manage')

@section('title','| Copy Files')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	@if($files)
		{!! Form::open(['route'=>'files.copy', 'data-parsley-validate'=>'']) !!}

		<div class="row">
			<div class="col-md-4 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
						@if (isset($search)) <span class="fas fa-search mr-4"></span>
						@else 				 <span class="fas fa-folder-open mr-4"></span>
						@endif 				 Copy Files to...
					</a>
				</h1>
			</div>

			<div class="col-md-4 mt-2">
				{{ Form::select('folder_id', $folders, false, ['class'=>'form-control custom-select', 'data-parsley-required'=>'', 'placeholder'=>'Select a destination folder...', 'autofocus']) }}
			</div>

			<div class="col-md-2 mt-2">
				{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block"><span class="fas fa-times-circle mr-2"></span>NO Cancel</a>') !!}
			</div>
			<div class="col-md-2 mt-2">
				{{ 	Form::button('<i class="fas fa-forward mr-2"></i>YES COPY NOW', ['type'=>'submit', 'class'=>'btn btn-primary btn-block']) }}
			</div>

			<div class="col-md-12">
				<hr>
			</div>
		</div>

		@include('partials.__filesCheck')

		{!! Form::close() !!}
	@endif
@endsection
