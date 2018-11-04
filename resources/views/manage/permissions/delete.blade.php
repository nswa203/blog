@extends('manage')

@section('title','| Manage Delete Permission')

@section('stylesheets')
@endsection

@section('content')
	@if($permission)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE PERMISSION {{ $permission->display_name }}</a></h1>
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

	 				@include('partials.__permissionsMeta')

					<div class="row">
						<div class="col-sm-12">
							{!! Form::open(['route'=>['permissions.destroy', $permission->id], 'method'=>'DELETE']) !!}
							{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Html::decode('<a href="Return" class="btn btn-outline-danger btn-block mt-3" onclick="window.history.back()"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
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
