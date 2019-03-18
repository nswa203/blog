@extends('manage')

@section('title','| Manage Delete Folder')

@section('stylesheets')
@endsection

@section('content')
	@if($folder)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE FOLDER {{ $folder->slug }}</a></h1>
				<hr>

				<h3>Name:</h3>
				<p class="lead">{!! $folder->name !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $folder->slug !!}</p>

				<h3>Description:</h3>
				<p class="lead">{!! $folder->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">

					@include('partials.__foldersMeta')

					{!! Form::open(['route'=>['folders.destroy', $folder->id], 'method'=>'DELETE']) !!}
					{{--
					<dt>
						@if($folder->photos->count()>0)
						<label for="delete-photos">
							{{ Form::checkbox('delete_photos', '1', null, ['class'=>'font-bold', 'hidden'=>'', 'id'=>'delete-photos']) }}
							<span class="span text-danger">
								Also DELETE THE
								{{ $folder->photos->count()==1 ? '1 Photo' : $folder->photos->count().' Photos' }}
								in this folder
							</span>
						</label>
						@else
							<span class="span text-success"> This Folder contains NO photos</span>
						@endif
					</dt>
					--}}
					<div class="row">
						<div class="col-sm-12">
							{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Html::decode('<a href="" class="btn btn-outline-danger btn-block mt-3" onclick="
								window.history.back();
								event.preventDefault ? event.preventDefault : event.returnValue=false;">
							<span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
						</div>
					</div>
					{!! Form::close() !!}
				</div>
				@if($folder->image)
					<img class="mt-4" src="{{ route('folders.getFolderFile', [$folder->id, 'Folder.jpg']) }}" width="100%" />
				@endif
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
