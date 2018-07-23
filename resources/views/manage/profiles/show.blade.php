@extends('manage')

@section('title','| Manage View Profile')

@section('stylesheets')
@endsection

@section('content')
	@if($profile)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user-circle mr-4"></span>View Profile</a></h1>
				<hr>
				@if($profile->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$profile->banner) }}" width="100%" />
					</div>
				@endif

				<h3>Name:</h3>
				<p class="lead">{!! $profile->user->name !!}</p>
				
				<h3>eMail:</h3>
				<p class="lead">{!! $profile->user->email !!}</p>

				<h3>Username:</h3>
				<p class="lead">{!! $profile->username !!}</p>

				<h3>About me:</h3>
				<p class="lead">{!! $profile->about_me !!}</p>

				<h3>Phone:</h3>
				<p class="lead">{!! $profile->phone !!}</p>

				<h3>Address:</h3>
				<p class="lead">{!! $profile->address !!}</p>				
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">

					@include('partials.__profilesMeta')

					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('profiles.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$profile->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['profiles.delete', $profile->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('profiles.index', '<i class="fas fa-user-circle mr-2"></i>See All Profiles', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>

				@if($profile->image)
					<div class="mt-3">
						<div id="image" style="display:block">
							<img src="{{ asset('images/'.$profile->image) }}" width="100%" />
						</div>
					</div>
				@endif

			</div>
		</div>

		@include('partials.__folders', ['count' => $folders->count(), 'zone' => 'User', 'page' => 'pageF'])

	@endif
@endsection

@section('scripts')
@endsection
