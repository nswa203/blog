@extends('main')

@section('stylesheets')
@endsection

@if ($folder)
	@section('title', "| $folder->slug")

	@section('content')
		<div class="row">
			<div class="col-md-8 offset-md-2 myWrap">
				@if($folder->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$folder->banner) }}" width="100%"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</div>
				@endif
					<a href="{{ asset($folder->directory.'/Folder.jpg') }}">
						<img src="{{ asset($folder->directory.'/Folder.jpg') }}" height="150px" class="img-frame float-left mr-4" style="margin-top:-10px; margin-bottom:10px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</a>
				<h1>Folder: {{ $folder->name }}</h1>
				<p>{!! $folder->description !!}</p>
				<div style="clear:both;">
					<hr>
					<p>
						Posted In: {{ $folder->category->name }}
						<span class="float-right">Published: {{ date('j M Y, h:i a', strtotime($folder->updated_at)) }}</span>
					</p>
				</div>
			</div>
		</div>
	@endsection
@endif

@section('scripts')
@endsection
