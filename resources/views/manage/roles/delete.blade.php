@extends('manage')

@section('title','| Manage Delete Role')

@section('stylesheets')
@endsection

@section('content')
	@if($role)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE ROLE {{ $role->display_name }}</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $role->display_name !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $role->name !!}</p>

				<h3>Description:</h3>
				<p class="lead">{!! $role->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ route('roles.show', $role->id) }}</a></dd>
						<dt class="col-sm-5">Role ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ $role->id }}</a></dd>
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-12">
							{!! Form::open(['route'=>['roles.destroy', $role->id], 'method'=>'DELETE']) !!}
								{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
								{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block mt-3"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
