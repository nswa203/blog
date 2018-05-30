@extends('manage')

@section('title','| Manage View Permission')

@section('stylesheets')
@endsection

@section('content')
	@if($permission)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-user mr-4"></span>View Permission</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $permission->display_name !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $permission->name !!}</p>

				<h3>Description:</h3>
				<p class="lead">{!! $permission->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('permissions.show', $permission->id) }}">{{ route('permissions.show', $permission->id) }}</a></dd>
						<dt class="col-sm-5">Permission ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('permissions.show', $permission->id) }}">{{ $permission->id }}</a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('permissions.edit', '<i class="fas fa-user-edit mr-2"></i>Edit', [$permission->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['permissions.delete', $permission->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('permissions.index', '<i class="fas fa-user-friends mr-2"></i>See All Permissions', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
