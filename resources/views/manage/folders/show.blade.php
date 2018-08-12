@extends('manage')

@section('title','| Manage View Folder')

@section('stylesheets')
@endsection

@section('content')
	@if($folder)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder mr-4"></span>Folder {{ $folder->name }}</a></h1>
				<hr>
				<a href="{{ route('private.getFolderFile', [$folder->id, 'Folder.jpg']) }}">
					<img src="{{ route('private.getFolderFile', [$folder->id, 'Folder.jpg']) }}" xwidth="150px" class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
				<p>Location: {{ $folder->path }}</p>
				<p>Folder:
					<a href="{{ route('folders.show', $folder->id) }}">{{ route('folders.show', $folder->id) }}</a>
				</p>	
				<p>Image:
					<a href="{{ route('private.getFolderFile', [$folder->id, 'Folder.jpg']) }}">{{ route('private.getFolderFile', [$folder->id, 'Folder.jpg']) }}</a>
				</p>
				<p>URL:
					<a href="{{ url('f/'.$folder->slug) }}">{{ url('f/'.$folder->slug) }}</a>
				</p>
				<p>Posts:
					<a href="{{ url('blog?pf='.$folder->slug) }}">{{ url('blog?pf='.$folder->slug) }}</a>
				</p>				
				<div style="clear:both;">
					<h4>{!! $folder->description !!}</h4>
				</div>	
				<hr>
			</div>	
		
			<div class="col-md-4">
				<div class="card card-body bg-light">
					
					@include('partials.__foldersMeta')

					<div class="row">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('files.createIn', '<i class="fas fa-folder-open mr-2"></i>Upload Files', [$folder->id], ['class'=>'btn btn-outline-primary btn-block'])) !!}
						</div>						
					</div>			

					<div class="row mt-3">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('folders.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$folder->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['folders.delete', $folder->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
		
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('folders.index', '<i class="fas fa-folder mr-2"></i>See All Folders', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>						
					</div>
				</div>
				@if($folder->image)
					{{-- <img src="{{ asset('images/'.$folder->image) }}" width="100%" class="mt-3"/> --}}
				@endif	
			</div>
		</div>

		@include('partials.__files',    ['count' => $folder->files->count(),    'zone' => 'Folder', 'page' => 'pageFi'])
		@include('partials.__posts',    ['count' => $folder->posts->count(),    'zone' => 'Folder', 'page' => 'pageP'])
		@include('partials.__profiles', ['count' => $folder->profiles->count(), 'zone' => 'Folder', 'page' => 'pagePr'])

	@endif
@endsection

@section('scripts')
@endsection
