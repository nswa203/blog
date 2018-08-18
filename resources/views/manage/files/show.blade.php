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

				<div class="col-md-5 mb-4" style="padding:0;">
					@if(substr($file->mime_type, 0, 2) == 'au' or substr($file->mime_type, 0, 2) == 'vi' or $file->ext == 'mp3')
						<a href="{{ route('files.showFile', [$file->id]) }}">
							<video controls poster="{{
									isset($meta->Picture) ? 'data:image/jpeg;base64,' . $meta->Picture :
									(substr($file->mime_type, 0, 2) == 'au' ? asset('favicon.ico') : '') }}"
								class="float-left mr-4 img-frame-lg">
			    				<source src="{{ route('private.getFile', [$file->id]) }}" type="{{ $file->mime_type }}" />
			    				<source src="{{ route('private.getFile', [$file->id]) }}" type="video/mp4" />
							</video>
						</a>
					@elseif(substr($file->mime_type, 0, 2) == 'im')
						<a href="{{ route('files.showFile', [$file->id]) }}">
							<img src="{{ route('private.getFile', [$file->id]) }}"
							class="float-left mr-4 img-frame-lg"
							style="width:100%; max-height:2000px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
						</a>
					@else
						<a href="{{ route('private.getFile', [$file->id]) }}">
							<img src="{{ route('private.findFile', [$file->ext, 'icons']) }}"
							class="float-left mr-4 img-frame-lg"
							style="width:100%; max-height:2000px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
						</a>
					@endif							 
					
				</div>

				<h5><a href="{{ route('files.showFile', [$file->id]) }}">
					<span class="float-left fas fa-binoculars mr-2" style="margin-top:-24px;"></span>
				</a></h5>

				@if (isset($meta->Caption))
					<p class="lead">{{ $meta->Caption }}</p>
				@endif

				<p>Stored: {{ filePath($file) }}</p>
				<p>Manage: {{ route('files.show', $file->id) }}</p>
				<p>URL:  {{ url('fi/'.$file->id) }}
				<p>Size: {{ mySize($file->size) }}</p>
				<p>Type: {{ $file->ext }} {{ $file->mime_type }}
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
						<div class="col-sm-6">
						<a href="{{ mySession('filesShow', 'indexURL') }}" class="btn btn-outline-dark btn-block"><span class="fas fa-undo mr-2"></span>Return</a>
						</div>

						<div class="col-sm-6">
						{!! Html::decode(link_to_route('files.index', '<i class="fas fa-folder-open mr-2"></i>See All Files', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
					
					@if ($list['x'])
						<div class="row mt-3">
							@for ($i=0; $i<=6; $i=$i+2)
								<div class="col-sm-3">
									<a href="{{ route('files.show', $list['x'][$i+1]) }}" class="btn btn-block btn-outline-dark
									{{ $list['x'][$i] }} {{ $list['x'][$i+1] ? '' : 'disabled' }}"></a>
								</div>
							@endfor
						</div>
					@endif
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 myWrap mt-2 ml-3">
					@if (isset($meta))
						@foreach($meta as $metaKey => $metaVal)
							@if (gettype($metaVal) == 'string' && $metaKey != 'Picture')
								<p>{{ $metaKey }} : {{ $metaVal }}</p>
							@endif 
						@endforeach
					@endif	
				</div>
			</div>
		</div>
{{--
		@include('partials.__profiles', ['count' => $file->profiles->count(), 'zone' => 'File', 'page' => 'pagePr'])
--}}
	@endif
@endsection

@section('scripts')
@endsection
