@extends('manage')

@section('title','| Manage Delete User')

@section('stylesheets')
@endsection

@section('content')
	@if($user)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE USER {{ $user->name }}</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $user->name !!}</p>
				
				<h3>eMail:</h3>
				<p class="lead">{!! $user->email !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('users.show', $user->id) }}">{{ route('users.show', $user->id) }}</a></dd>
						<dt class="col-sm-5">Profile:</dt>
						<dd class="col-sm-7">
							@if($user->profile['id'])
								<a href="{{ route('profiles.show', $user->profile['id']) }}">{{ $user->profile['username'] }}</a>
							@endif
						</dd>							
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-12">
							{!! Form::open(['route'=>['users.destroy', $user->id], 'method'=>'DELETE']) !!}
								{{  Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
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
