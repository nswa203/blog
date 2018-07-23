@extends('manage')

@section('title','| Manage Delete User')

@section('stylesheets')
@endsection

@section('content')
	@if($profile)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE PROFILE {{ $profile->username }}</a></h1>
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
						<div class="col-sm-12">
							{!! Form::open(['route'=>['profiles.destroy', $profile->id], 'method'=>'DELETE']) !!}
								{{  Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
								{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block mt-3"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
							{!! Form::close() !!}
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
	@endif
@endsection

@section('scripts')
@endsection
