@extends('manage')

@section('title','| Manage View Role')

@section('stylesheets')
@endsection

@section('content')
	@if($role)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user mr-4"></span>View Role</a></h1>
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

					@include('partials.__rolesMeta')

					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('roles.edit', '<i class="fas fa-user-edit mr-2"></i>Edit', [$role->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['roles.delete', $role->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('roles.index', '<i class="fas fa-user-friends mr-2"></i>See All Roles', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		@include('partials.__permissions', ['count' => $role->permissions->count(), 'zone' => 'Role', 'page' => 'pagePm'])
		@include('partials.__users',       ['count' => $role->users->count(),       'zone' => 'Role', 'page' => 'pageU'])

	@endif
@endsection

@section('scripts')
@endsection
