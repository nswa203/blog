@extends('manage')

@section('title','| Manage View File')

@section('stylesheets')
@endsection

@section('content')
	@if ($file)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder mr-4"></span>File {{ $file->title }}</a></h1>
				<hr>
      
				<div class="col-md-5 mb-4" style="padding:0;">
					@if(substr($file->mime_type, 0, 2) == 'au' or substr($file->mime_type, 0, 2) == 'vi' or $file->ext == 'mp3')
						<a href="{{ route('files.showFile', [$file->id]) }}">
							<video controls poster="{{
									isset($meta->Picture) ? 'data:image/jpeg;base64,' . $meta->Picture :
									(substr($file->mime_type, 0, 2) == 'au' ? asset('favicon.ico') : '') }}"
onLoadedData="myVolume('session')"
onEnded="myPlayList('{{ $file->mime_type }}')" {{ $list['x'][10]!=0 ? 'autoplay' : '' }}
								class="float-left mr-4 img-frame-lg" onLoadedData="myVolume('session')">
			    				<source src="{{ route('private.getFile', [$file->id]) }}" type="{{ $file->mime_type }}" />
			    				<source src="{{ route('private.getFile', [$file->id]) }}" type="video/mp4" />
							</video>
						</a>
					@elseif(substr($file->mime_type, 0, 2) == 'im')
						{{-- r=n no_auto_rotate t=500 load_with_thumbnail_size --}}
						<a href="{{ route('files.showFile', [$file->id]) }}">
							<img src="{{ route('private.getFile', [$file->id, 'r=n&t=500']) }}"
							class="float-left mr-4 img-frame-lg"
							style="width:100%; max-height:2000px;"
onLoad="setTimeout(function(){ myPlayList('{{ $file->mime_type }}')}, 5000)"
							onerror="this.onerror=null; myImgDebug(this);" 
							xonerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" /> 
						</a>  
					@elseif (strtolower($file->ext) == 'gpx' && ( url('/') == 'https://nswa203.asuscomm.com' || url('/') == 'http://nswa203.asuscomm.com' ) )
						<div class="container-fluid float-left ml-0 mr-4 img-frame-lg" style="width:100%; height:28vh; margin-left:-15px;">
							<a href="{{ route('private.getFile', [$file->id]) }}">
								<div id="map" style="width:100%; height:100%;">
<script>setTimeout(function(){ myPlayList('{{ $file->mime_type }}')}, 7000)</script>
								</div>
							</a>
						</div>
					@else
						{{-- Here we provide icons for different filetype extensions                                  --}}
						{{-- They should be pre-loaded into the db in $folder->name=icons $file->title=file_extension --}}
					    {{-- Debug: https://blog/manage/private/find/pdf/icons                                        --}}
						<a href="{{ route('private.getFile', [$file->id]) }}">
							<img src="{{ route('private.findFile', [pathinfo($file->file, PATHINFO_EXTENSION), 'icons']) }}" 
							class="float-left mr-4 img-frame-lg"
							style="width:100%; max-height:2000px;"
onLoad="setTimeout(function(){ myPlayList('{{ $file->mime_type }}')}, 5000)"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
						</a>
					@endif	
				</div>

				<h5><a href="{{ route('files.showFile', [$file->id, substr($file->mime_type, 0, 2) == 'im' ? 'r=y' : '']) }}">
					<span class="float-left fas fa-expand fa-2x clean-link mr-3" data-toggle="tooltip" data-placement="right" title="Zoom in" style="margin-top:-24px;focus { outline: 0; };"></span>
				</a></h5>

				@if (isset($meta->Caption))
					<p class="lead">{{ $meta->Caption }}</p>
				@endif

				<p>Stored: {{ filePath($file) }}</p>
				<p>Manage: {{ route('files.show', $file->id) }}</p>
				<p>URL: {{ url('fi/'.$file->id) }}
				<p>getFile: {{ route('private.getFile', [$file->id]) }}</p>
				<p>Zoom: {{ route('files.showFile', [$file->id, 'r=y']) }}</p>
				<p>Size: {{ mySize($file->size) }}</p>
				<p>Type: {{ $file->ext }} {{ $file->mime_type }}
				<hr>

				<div class="tags float-left">
					@foreach ($file->tags as $tag)
						<a href="{{ route('tags.show', $tag->id) }}"><span class="badge badge-info mb-2">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					
					@include('partials.__filesMeta')
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('files.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$file->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['files.many']]) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block', 'name'=>'choice', 'value'=>'delete,'.$file->id]) }}
							{!! Form::close() !!}
						</div>
					</div>
		
					<div class="row mt-3">
						<div class="col-sm-6">
						<a href="{{ mySession('filesShow', 'indexURL') }}" class="btn btn-outline-dark btn-block"><span class="fas fa-undo mr-2"></span>Return</a>
						</div>

						<div class="col-sm-6">
						{!! Html::decode(link_to_route('files.index', '<i class="fas fa-folder mr-2"></i>See All Files', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>

					@if ($list['x']) {{-- Items navigation --}}
					    @include('partials._nav_show', ['data' => $list['x'], 'icons' => [
					    	'fas fa-fast-backward',
					    	'fas fa-step-backward',
					    	'fas fa-stop',
					    	['f-pause fas fa-pause', 'f-play fas fa-play text-danger', 'f-loop fas fa-retweet text-danger',
					    	 'f-playback fas fa-play fa-flip-horizontal text-danger', 'f-loopback fas fa-retweet text-danger'],
					    	'fas fa-step-forward',
					    	'fas fa-fast-forward']
					    ])
					@endif
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12 myWrap mt-2 ml-3">
					@if (isset($meta))
						@foreach($meta as $metaKey => $metaVal)
							@if (gettype($metaVal) != 'array' && $metaKey != 'Picture')
								<p>{{ $metaKey }} : {{ $metaVal }}</p>
							@endif 
						@endforeach
					@endif
					SHA256 Hash: {{ $file->sha256 }}	
				</div>
			</div>
		</div>
{{--
		@include('partials.__profiles', ['count' => $file->profiles->count(), 'zone' => 'File', 'page' => 'pagePr'])
--}}
	@endif
@endsection

@section('scripts')
	@if (strtolower($file->ext) == 'gpx' && ( url('/') == 'https://nswa203.asuscomm.com' || url('/') == 'http://nswa203.asuscomm.com' ) )
		@include('partials._javascriptOSmap')

		<script type= "text/javascript">
			// ========================================================================== //
			// Handles Ordnance Survey maps
			// initmapbuilder(divID, [file.gpx])
			// eg initMapBuilder('map', "{{ route('private.getFile', [$file->id]) }}");		
			function initMapBuilder(id, fgpx=false) {
				var options = {	resolutions: [500, 200, 100, 50, 25, 10, 5, 4, 2.5, 2, 1] };
				osMap = new OpenSpace.Map(id, options);

				if (fgpx) {												// Load GPX file
					lgpx = new OpenLayers.Layer.GML("gpx", fgpx, {
						format: OpenLayers.Format.GPX,
					    style: {strokeColor: "black", strokeWidth: 8, strokeOpacity: 0.6},
					    projection: new OpenLayers.Projection("EPSG:4326")
			        });
					osMap.addLayer(lgpx);
					osMap.zoomToExtent(lgpx.getDataExtent());
					lgpx.events.register("loadend", lgpx, function() {
						this.map.zoomToExtent(this.getDataExtent());
					});
				} else {												// Just show UK map
					osMap.setCenter(new OpenSpace.MapPoint(405000, 240000), 0);
				}	
			}
		</script>

		<script type= "text/javascript">
			$(document).ready(function() {
				initMapBuilder('map', "{{ route('private.getFile', [$file->id]) }}");
			})
		</script>
	@endif			
@endsection
