@extends('manage')

@section('title','| Manage View User')

@section('stylesheets')
@endsection

@section('content')
	@if($user)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user mr-4"></span>View User Details</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $user->name !!}</p>
				
				<h3>eMail:</h3>
				<p class="lead">{!! $user->email !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">

					@include('partials.__usersMeta')

					@if(!$user->profile)					
						<div class="row">
							<div class="col-sm-12">
								{!! Html::decode(link_to_route('profiles.create', '<i class="fas fa-user-circle mr-2"></i>Add A User Profile', [$user->id], ['class'=>'btn btn-outline-dark btn-block'])) !!}
							</div>
						</div>
						<hr class="hr">
					@endif	
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('users.edit', '<i class="fas fa-user-edit mr-2"></i>Edit', [$user->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['users.delete', $user->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('users.index', '<i class="fas fa-user-friends mr-2"></i>See All Users', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		@include('partials.__albums',      ['count' => $user->albums->count(),  'zone' => 'User', 'page' => 'pageA'])
		@include('partials.__folders',     ['count' => $user->folders->count(), 'zone' => 'User', 'page' => 'pageF'])
		@include('partials.__posts',       ['count' => $user->posts->count(),   'zone' => 'User', 'page' => 'pageP'])
		@include('partials.__roles',	   ['count' => $user->roles->count(),   'zone' => 'User', 'page' => 'pageR'])
		@include('partials.__permissions', ['count' => $permissions->count(),   'zone' => 'User', 'page' => 'pagePm'])

	@endif
@endsection

@section('scripts')
@endsection
