@extends('manage')

@section('title','| Edit Files Status')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 	   !!}
	{!! Html::style('css/select2.min.css') !!}
@endsection

@section('content')
	@if($files)
		{!! Form::open(['route'=>['files.updateMultiple'], 'data-parsley-validate'=>'']) !!}

		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
						@if (isset($search)) <span class="fas fa-search mr-4"></span>
						@else 				 <span class="fas fa-folder-open mr-4"></span>
						@endif 				 Edit Files' Status
					</a>
				</h1>
			</div>

			<div class="col-md-2 mt-2">
				{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block"><span class="fas fa-times-circle mr-2"></span>NO Cancel</a>') !!}
			</div>
			<div class="col-md-2 mt-2">
				{{ 	Form::button('<i class="far fa-edit mr-2"></i>YES EDIT NOW', ['type'=>'submit', 'class'=>'btn btn-primary btn-block']) }}
			</div>
		</div>

		<div class="row">
			<div class="col-md-1 offset-md-7">
				{{ Form::label('status', 'Status:', ['class'=>'font-bold mt-3 float-right']) }}
			</div>
			<div class="col-md-4 mt-2">
				{{ Form::select('status[]', $list['f'], null, ['class'=>'form-control select2-single', 'multiple'=>'']) }}
			</div>				

			<div class="col-md-1 offset-md-7">
				{{ Form::label('tags', 'Tags:', ['class'=>'font-bold mt-4 float-right']) }}
			</div>
			<div class="col-md-4 mt-3">
				{{ Form::select('tags[]', $tags, null, ['class'=>'form-control select2-multi', 'multiple'=>'']) }}
			</div>

			<div class="col-md-12">
				<hr>
			</div>
		</div>

		@include('partials.__filesCheck')

		{!! Form::close() !!}
	@endif
@endsection
