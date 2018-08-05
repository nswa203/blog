@extends('manage')

@section('title','| Manage View Image')

@section('stylesheets')
@endsection

@section('content')
	@if($photo)
		<div class="row">
			<div class="col-md-12 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-image mr-4"></span>{{ $photo->title }}</a></h1>
				<hr>
				<div class="text-center">
					<a href="{{ asset('images/'.$photo->file) }}">
						<img src="{{ asset('images/'.$photo->image) }}"
							class="img-frame-lg" style="margin-top:0px; margin-bottom:10px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</a>
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
