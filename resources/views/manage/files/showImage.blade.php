@extends('main')

@section('title','| Manage Zoom File')

@section('stylesheets')
@endsection

@section('navControls')
	@if ($list['x'])
		<div class="row lt-2">
			@for ($i=0; $i<=10; $i=$i+2) 
				<div class="{{ $i<10?'mr-2':'' }}"> 									{{-- FIRST PREVIOUS NEXT LAST --}}
					@if ($i==4)
						<a href="{{ route('files.show', $file->id) }}" data-toggle="tooltip" data-placement="bottom" title="Return"
					@elseif ($i==6) 													{{-- STOP --}}
						<a href="#" data-toggle="tooltip" data-placement="bottom" title="Play All"
						onClick="myToggle(this, 'fa-genderless', 'fa-reply', 'fa-reply-all');
								setTimeout(function(){ myPlayList('navBtn-6', 'navBtn-8', 'navBtn-0', true)}, 5000)"
					@else 																{{-- PLAYLIST --}}
						<a href="{{ route('files.showFile', $list['x'][$i+1]) }}"
					@endif
					onClick="myVolume()"
					id="navBtn-{{ $i }}" class="btn btn-block btn-outline-secondary
					{{ $list['x'][$i] }} {{ $list['x'][$i+1] ? '' : 'disabled' }}"></a>
				</div>
			@endfor
		</div>
		<div class="row lt-2"> 															{{-- RANGE BAR --}}
			<input disabled type="range" class="custom-range" min="1" max="{{ $list['x'][12] }}"
			value="{{ $list['x'][13] }}" style="display:{{ $list['x'][12]>1 ? 'block' : 'none' }};">
		</div>
	@endif
@endsection

@section('contentLarge')
	@if($file)
		<div class="row">
			<div class="col-md-12 myWrap">

					@if(substr($file->mime_type, 0, 2) == 'au' or substr($file->mime_type, 0, 2) == 'vi' or $file->ext == 'mp3')
						@if((substr($file->mime_type, 0, 2) == 'au' or $file->ext == 'mp3') && isset($meta))
							<div class="text-center mb-4" >
								<h4>
									{{ isset($meta->Performer) ? $meta->Performer . ' /' : '' }}
									{{ isset($meta->Album    ) ? $meta->Album     . ' /' : '' }}
									{{ isset($meta->Title    ) ? $meta->Title            : '' }}
								<h4>
							</div>
						@endif
						<a href="{{ route('files.show', [$file->id]) }}">
							<div style="object-fit:contain; margin:0 auto; height:91vh;	width:{{ substr($file->mime_type, 0, 2) == 'vi' ? '100%' : '50%' }};">
								<video controls autoplay onEnded="myPlayList('navBtn-6', 'navBtn-8', 'navBtn-0')"
									   onLoadedData="myVolume('session')"
									   poster="{{ isset($meta->Picture) ? 'data:image/jpeg;base64,' . $meta->Picture :
									   (substr($file->mime_type, 0, 2) == 'au' ? asset('favicon.ico') : '') }}">
				    				<source src="{{ route('private.getFile', [$file->id]) }}" type="{{ $file->mime_type }}" />
				    				<source src="{{ route('private.getFile', [$file->id]) }}" type="video/mp4" />
								</video>
							</div>
						</a>	
					@elseif(substr($file->mime_type, 0, 2) == 'im')
						<a href="{{ route('files.show', [$file->id]) }}">
							<img src    ="{{ route('private.getFile', [$file->id, 'r=y']) }}"
								 style  ="object-fit:contain; width:100%; height:91vh;);"
								 onLoad ="setTimeout(function(){ myPlayList('navBtn-6', 'navBtn-8', 'navBtn-0')}, 5000)"
	 							 onerror="this.onerror=null; myImgDebug(this);" 
								 xonError="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
						</a>
					@elseif($file->ext == 'gpx')
						<div class="col-md-8 offset-md-2 text-center">
							<h3>{{ $file->title }}</h3>
							<div id="map" class="img-frame-lg" style="width:100%; height:80vh;"></div>
							<div id="OSscaleline" class="float-left mt-1" style="margin-bottom:-1vh;"></div>
							<div id="OSscale"></div>
							{{ isset($meta->Distance) ? 'Distance = '.$meta->Distance.' miles' : '' }}
							<div class="mt-4 img-frame-lg" id="app">
								<elevations></elevations>
							</div>
						</div>
					@else
						<div class="text-center">
							<a href="{{ route('private.getFile', [$file->id]) }}">
								Click to Open {{ $file->mime_type }}
								<img src="{{ route('private.getFile', [$file->id]) }}"
								 style="object-fit:contain; width:100%; height:91vh;"
								 onError="this.onerror=null; this.src='{{ asset('favicon.ico') }}';">
							</a>
						</div>		
 					@endif

			</div>
		</div>
	@endif
@endsection

@section('scripts')
	{!! Html::script('js/app.js') 	  !!}

	@if (strtolower($file->ext) == 'gpx' && ( url('/') == 'https://nswa203.asuscomm.com' || url('/') == 'http://nswa203.asuscomm.com' ))
		@include('partials._javascriptOSmap')

		<script type= "text/javascript">
			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Converts waypoints to markers
			function myWaypoints() {
	            //console.log('myWaypoints:');
				var meta = {!! json_encode($meta) !!};
				if (typeof(meta['wpt'])=='undefined') { j=0; } else { j=meta['wpt'].length; } 
				for(i=0; i<j; ++i) {
					wp = meta['wpt'][i];
					if      (i==0) { colour = 'red';   }				// First Marker
					else if (i==j) { colour = 'green'; }				// Last Marker 
					else 		   { colour = 'blue';  }				// Intermediate Marker
					if(!wp['link' ]) { wp['link' ]=''; }
					if(!wp['image']) { wp['image']=''; }
					link  = wp['link' ].replace(/"/g, '').replace(/'/g, '');	// strip quotes
					image = wp['image'].replace(/"/g, '').replace(/'/g, '');	// strip quotes
					addMarker(wp['lon'], wp['lat'], colour, wp['name'], link, image);
				}

				var markers = osMap.getMarkerLayer();					// Force Marker layer Top
				markers.setZIndex(lgpx.getZIndex()+1);		
			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Adds a marker to the marker layer with optional text, link, image
			function addMarker(lon, lat, colour='red', text='', link=false, image=false) {
	            //console.log('addMarker: '+lon+', '+lat+', '+colour+', '+text+', '+link+', '+image);
				var pos        = new OpenSpace.MapPoint(lon, lat);
				var iconFile   = '/images/marker_'+colour+'.png';			// Point at our icon
				var sizeW      = 30;
				var sizeH      = sizeW+10;
				var size       = new OpenLayers.Size(sizeW, sizeH);
				var offset     = new OpenLayers.Pixel(-(size.w/2), -size.h);
				var infoAnchor = new OpenLayers.Pixel(sizeW/3*2, 5);
				var icon       = new OpenSpace.Icon(iconFile, size, offset, null, infoAnchor);

				if (image) {
					var infoHtml = '';
					if (text) { infoHtml = infoHtml+'<p>'+text+'</p>'; }
					if (link) { infoHtml = '<a href="'+link+'">'+infoHtml+'</a>'; }  
					infoHtml = infoHtml+'<a href="'+image+'"><img src='+image+'?t=200'+' style="max-width: 200px; max-height:100px; width:auto; height:auto;);"/></a>';
					var infoSize = new OpenLayers.Size(220, 210);
				} else {
					var infoHtml = '<p>'+text;
					if (link) {
						infoHtml = infoHtml+'<a href="'+link+'"><span class="fas fa-link ml-2"></span></a>';
					}
					infoHtml = infoHtml+'</p>';
					var infoSize = new OpenLayers.Size(220, 130);
				}

				//console.log(infoHtml);
				osMap.createMarker(pos, icon, infoHtml, infoSize);
			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// initmapbuilder(divID, [file.gpx])
			// eg initMapBuilder('map', "{{ route('private.getFile', [$file->id]) }}");
 			function initMapBuilder(id, fgpx=false) {
	            //console.log('initMapBuilder: '+id);
				//var fgpx = "{{ route('private.getFile', [$file->id]) }}"
				var options = {	resolutions: [500, 200, 100, 50, 25, 10, 5, 4, 2.5, 2, 1] };
				osMap = new OpenSpace.Map(id, options);
				setglobaloptions();
				//setmapbuilderoptions();
				makegrid();												// Adds Cursor Tracking box		
				addSearchBox(1);										// Adds Search Box										
				if (fgpx) {												// Load GPX file

					OpenLayers.Util.onImageLoadError = function() {console.log("error");}; 

					lgpx = new OpenLayers.Layer.GML("gpx", fgpx, {
						format: OpenLayers.Format.GPX,
					    style: {strokeColor: "blue", strokeWidth: 5, strokeOpacity: 0.4},
					    projection: new OpenLayers.Projection("EPSG:4326")
			        });
					osMap.addLayer(lgpx);
					osMap.zoomToExtent(lgpx.getDataExtent());
					lgpx.events.register("loadend", lgpx, function() {
						this.map.zoomToExtent(this.getDataExtent());
					});
					var scaleline = new OpenLayers.Control.ScaleLine({
						div: document.getElementById("OSscaleline"),
						maxWidth: 200,
					});
					osMap.addControl(scaleline);
					var scale = new OpenLayers.Control.Scale();
					scale.div = document.getElementById("OSscale");
					osMap.addControl(scale);
				
					myWaypoints();
				} else {												// Just show UK map
					osMap.setCenter(new OpenSpace.MapPoint(430000, 270000), 0);
				}	
			}		
		</script>

		<script type= "text/javascript">
			// ========================================================================== //
			// Handles Ordnance Survey map elevations			
			var app=new Vue({
				el: '#app',
				data: {
					api_token: '{{ Auth::user()->api_token }}',
					resource_id: '{{ $file->id }}',
					route: "{{ route('api.files.elevation', [$file->id]) }}",
					results: '{}',
				},
			});
		</script>

		<script type= "text/javascript">
			// ========================================================================== //
			// Wait for page load before rendering Ordnance Survey map
			$(document).ready(function() {
				initMapBuilder('map', "{{ route('private.getFile', [$file->id]) }}");
			})
		</script>
	@else
		<script type= "text/javascript">
			html="<img src={{ asset('favicon.ico') }} style='width:92%;' />";
			el=_('map');
			el.innerHTML=html;
		</script>	
	@endif
	
		<script type= "text/javascript">
		// ========================================================================== //
		// Handles PlayNext & PlayLoop
		// NS01 
		function myPlayList(btnPlay ,btnPlayNext, btnPlayLoop, timer=false) {	// NS01
			console.log(btnPlay+' '+btnPlayNext+' '+btnPlayLoop);
			if (timer) {    	// NS01 Ignore if timer event & we have Audio/Video 
				el=document.getElementsByTagName('video');
				if (el!='Undefined') {
					console.log('Ignore timer event for Audio/Video - should be triggered by onEnded!');
					return;
				}
			}

			var elPlay=document.getElementById(btnPlay);
			if (elPlay.classList.contains('fa-reply')) {
				var elButton=document.getElementById(btnPlayNext);
				elButton.href=elButton.href+'?pl=2';
				myClick(btnPlayNext);
			} else if (elPlay.classList.contains('fa-reply-all')) {
				var elButton=document.getElementById(btnPlayNext);
				elButton.href=elButton.href+'?pl=3';
				var elButton=document.getElementById(btnPlayLoop);
				elButton.href=elButton.href+'?pl=3';
				myClick(btnPlayNext, btnPlayLoop);
			}
		}

		// ========================================================================== //
		// Simulates a button click with fallback button if not enabled  
		function myClick(id, idAlt=false) {
			var elButton=document.getElementById(id);
			var enabled=!elButton.classList.contains('disabled');
			if (!enabled && idAlt) {
				id=idAlt;
				elButton=document.getElementById(id);
				enabled=!elButton.classList.contains('disabled');
			}
			//alert('myClick: '+id+' '+elButton+' '+enabled);
			if (enabled) { elButton.click(); }
		}

		// ========================================================================== //
		// n-way toggle 
		function myToggle($this, state1, state2, state3) {
			var states=[state1, state2, state3];
			for (i=0; i<states.length; ++i) {
				if (i==states.length-1) { j=0 } else { j=i+1 }
				if ($this.classList.contains(states[i])) {
					$this.classList.remove(states[i]);
					$this.classList.add(states[j]);
					break;
				}
			}
		}
	</script>
@endsection
