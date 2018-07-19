@extends('manage')

@section('title','| Manage Edit Permission')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user-edit mr-4"></span>Edit Permission</a></h1>
			<hr>
			{!! Form::model($permission, ['route'=>['permissions.update', $permission->id], 'method'=>'PUT', 'data-parsley-validate'=>'']) !!}
			
			{{ 	Form::label('display_name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('display_name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'autofocus'=>'']) }}
	
			{{ 	Form::label('name', 'Slug:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('name', null, ['class'=>'form-control', 'disabled'=>'']) }} 

			{{ 	Form::label('description', 'Description:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('description', null, ['class'=>'form-control', 'data-parsley-maxlength'=>'191']) }} 
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">URL:</dt>
					<dd class="col-sm-7">
						<a href="{{ route('permissions.show', $permission->id) }}">{{ route('permissions.show', $permission->id) }}</a>
					</dd>
					<dt class="col-sm-5">Permission ID:</dt>
					<dd class="col-sm-7"><a href="{{ route('permissions.show', $permission->id) }}">{{ $permission->id }}</a></dd>
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->created_at)) }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->updated_at)) }}</dd>
				</dl>
				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-user-edit mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('permissions.index', '<i class="fas fa-user-friends mr-2"></i>See All Permissions', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>
		{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js')	!!}
@endsection
