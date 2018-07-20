@extends('manage')

@section('title','| Manage View Folder')

@section('stylesheets')
@endsection

@section('content')
	@if($folder)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder mr-4"></span>Folder {{ $folder->name }}</a></h1>
				<hr>
				<a href="{{ route('private.getfile', [$folder->id, 'Folder.jpg']) }}">
					<img src="{{ route('private.getfile', [$folder->id, 'Folder.jpg']) }}" xwidth="150px" class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
				<p class="lead">{!! $folder->description !!}</p>
				<p>Location: {{ $folder->path }}</p>
				<p>URL: {{ route('private.getfile', [$folder->id, '']) }}</p>
				<p>Size: {{ round($folder->max_size/1000000) }}M</p>
				<p>Used: {{ round(($folder->size / $folder->max_size) * 100, 2) }}%</p>
				<hr>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url('f/'.$folder->slug) }}">{{ url('f/'.$folder->slug) }}</a></dd>
						<dt class="col-sm-5">Folder ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('albums.show', $folder->id) }}">{{ $folder->id }}</a></dd>
						<dt class="col-sm-5">Category:</dt>						
						<dd class="col-sm-7">
							<a href="{{ route('categories.show', [$folder->category->id, session('zone')]) }}"><span class="badge badge-info">{{ $folder->category->name }}</span></a>
						</dd>
						<dt class="col-sm-5">Status:</dt>						
						<dd class="col-sm-7 {{ $folder->size / $folder->max_size > .85 ? 'text-danger' : 'text-success' }}">
							{{ $status_list[$folder->status] }},
							{{ round(($folder->size / $folder->max_size) * 100, 2) }}% Used 
						</dd>								
						<dt class="col-sm-5">Author:</dt>
						<dd class="col-sm-7">
							@if($folder->user->id)
								<a href="{{ route('users.show', $folder->user->id) }}">{{ $folder->user->name }}</a>
							@endif
						</dd>		
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($folder->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($folder->updated_at)) }}</dd>
					</dl>

					<hr class="hr-spacing-top">
					<div class="row">
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
	@endif
@endsection

@section('scripts')
@endsection
