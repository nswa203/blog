@extends('main')

@section('stylesheets')
@endsection

@if ($file)
	@section('title', "| $file->title")

	@section('content')
		<div class="row">
			<div class="col-md-8 offset-md-2 myWrap">
				@if($file->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$file->banner) }}" width="100%"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</div>
				@endif
					<a href="{{ asset(fileURL($file)) }}">
						<img src="{{ asset(fileURL($file)) }}" height="150px" class="img-frame float-left mr-4" style="margin-top:-10px; margin-bottom:10px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</a>
				<h1>File: {{ $file->title }}</h1>
				<p>{!! $file->file !!}</p>
				<div style="clear:both;">
					<hr>
					<p>
						@foreach ($file->tags as $tag)
							<span class="badge badge-info">{{ $tag->name }}</span>
						@endforeach
					</p>		
					<p>
						Posted In: {{ $file->folder->name }}
						<span class="float-right">Published: {{ date('j M Y, h:i a', strtotime($file->published_at)) }}</span>
					</p>
		
				</div>
			</div>
		</div>
	@endsection
@endif

@section('scripts')
@endsection
