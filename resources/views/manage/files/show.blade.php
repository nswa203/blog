@extends('manage')

@section('title','| Manage View File')

@section('stylesheets')
@endsection

@section('content')
	@if($file)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder-open mr-4"></span>File {{ $file->title }}</a></h1>
				<hr>
				<a href="{{ route('private.getFile', [$file->id]) }}">
					<img src="{{ route('private.getFile', [$file->id]) }}" xwidth="150px" class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
				<p class="lead">{!! $file->description !!}</p>
				<p>Location: {{ $file->path }}</p>
				<p>URL: {{ route('private.getFile', [$file->id]) }}</p>
				<p>Size: {{ mySize($file->size) }}</p>
				<hr>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					
					@include('partials.__filesMeta')

					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('files.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$file->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['files.delete', $file->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
		
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('files.index', '<i class="fas fa-folder mr-2"></i>See All Files', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>						
					</div>
				</div>
				@if($file->image)
					{{-- <img src="{{ asset('images/'.$file->image) }}" width="100%" class="mt-3"/> --}}
				@endif	
			</div>
		</div>
{{--
		@include('partials.__profiles', ['count' => $file->profiles->count(), 'zone' => 'File', 'page' => 'pagePr'])
--}}
	@endif
@endsection

@section('scripts')
@endsection
