@extends('main')

@section('title','| Manage Zoom File')

@section('stylesheets')
@endsection

@section('navControls')
	@if ($list['x']) {{-- Items navigation --}}
	    @include('partials._nav_showImage', ['data' => $list['x'], 'icons' => [
	    	'fas fa-fast-backward',
	    	'fas fa-step-backward',
	    	'fas fa-stop',
	    	['f-pause fas fa-pause', 'f-play fas fa-play text-danger', 'f-loop fas fa-forward text-danger',
	    	 'f-playback fas fa-play fa-flip-horizontal text-danger', 'f-loopback fas fa-backward text-danger'],
	    	'fas fa-step-forward',
	    	'fas fa-fast-forward']
	    ])
	@endif
@endsection

@section('contentLarge')
	@if($file)
		<div class="row">
			<div class="col-md-12 myWrap clean-link">
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
				@elseif (strtolower($file->ext) == 'gpx' && ( url('/') == 'https://nswa203.asuscomm.com' || url('/') == 'http://nswa203.asuscomm.com' ) )
					<div class="col-md-8 offset-md-2 text-center clean-link">
						<a href="{{ route('private.getFile', [$file->id]) }}">
							<h3>{{ $file->title }}</h3>
						</a>
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
					{{-- Here we provide icons for different filetype extensions                                  --}}
					{{-- They should be pre-loaded into the db in $folder->name=icons $file->title=file_extension --}}
				    {{-- Debug: https://blog/manage/private/find/pdf/icons 										  --}}
				    	<a href="{{ route('private.getFile', [$file->id]) }}">
							<h3>{{ $file->title }}</h3>
							<img src="{{ route('private.findFile', [$file->ext, 'icons']) }}"
							 style="object-fit:contain; width:100%; height:88vh;"
							 onError="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
						</a>
					</div>
				@endif
			</div>
		</div>
	@endif
@endsection

@section('scripts')
	 {!! Html::script('js/app.js') 	  !!}

		<script type= "text/javascript">


		// ========================================================================== //
		// Handles PlayNext & PlayLoop
		// NS01 
		function myPlayList(btnPlay ,btnPlayNext, btnPlayLoop, timer=false) {	// NS01
			console.log('myPlayList:', btnPlay, btnPlayNext, btnPlayLoop, timer);
			if (timer) {    	// NS01 Ignore if timer event & we have Audio/Video 
				var el=document.getElementsByTagName('video');
				if (el!='Undefined') {
					console.log('Ignore timer event for Audio/Video - should be triggered by onEnded!');
					return;
				}
			}

			var elPlay=document.getElementById(btnPlay);
console.log('myPlayList: TR01', btnPlay, elPlay, elPlay.classList);

			if (elPlay.classList.contains('fa-reply')) {
				var elButton=document.getElementById(btnPlayNext);
				elButton.href=elButton.href+'?pl=2';
				myClick(btnPlayNext);
console.log('myPlayList: TR02');
			} else if (elPlay.classList.contains('fa-reply-all')) {
				var elButton=document.getElementById(btnPlayNext);
				elButton.href=elButton.href+'?pl=3';
				var elButton=document.getElementById(btnPlayLoop);
				elButton.href=elButton.href+'?pl=3';
				myClick(btnPlayNext, btnPlayLoop);
console.log('myPlayList: TR03');

			}
		}

		// ========================================================================== //
		// Simulates a button click with fallback button if not enabled  
		function myClick(id, idAlt=false) {
			console.log('myClick:', id, idAlt);
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
		// n-way toggle multi-stage classes that have multiple components 
		function myToggle2(id, classes=[]) {
			var el=document.getElementById(id);
			console.log('myToggle2:', id, classes);
			for (var i=0; i<classes.length; ++i) {
				var statesOld=classes[i].split(' ');
				var hit=false;
				for (var j=0; j<statesOld.length; ++j) { 				// Check if ALL classes present
					hit=el.classList.contains(statesOld[j]);
					if (! hit) { break; }
				}
				if (hit) { 												// Flip to next state
					if (i==classes.length-1) { j=0; } else { j=i+1; }
					var statesNew=classes[j].split(' ');					
					for (j=0; j<statesOld.length; ++j) { 				// Remove each Old class 
						el.classList.remove(statesOld[j]);
					}
					for (j=0; j<statesNew.length; ++j) { 				// Add each New class
						el.classList.add(statesNew[j]);
					}
					break;
				}
			}
		}

		// ========================================================================== //
		// n-way toggle 
		function myToggle($this, state1, state2, state3) {
			console.log($this);
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

</script>
	@if (strtolower($file->ext) == 'gpx' && ( url('/') == 'https://nswa203.asuscomm.com' || url('/') == 'http://nswa203.asuscomm.com' ))
		@include('partials._javascriptOSmap')

		<script type= "text/javascript">
			/* Copyright Pierre GIRAUD, https://gist.github.com/pgiraud/6131715
			 * Published under WTFPL license. 
			 * @requires OpenLayers/Renderer/SVG.js
			 */
			OpenLayers.Renderer.SVGExtended = OpenLayers.Class(OpenLayers.Renderer.SVG, {
			    eraseGeometry: function(geometry, featureId) {
			        this.removeArrows(geometry);
			        return OpenLayers.Renderer.SVG.prototype.eraseGeometry.apply(this, arguments);
			    },

			    drawFeature: function(feature, style) {
			        if (feature.geometry) {
			            this.removeArrows(feature.geometry);
			        }
			        return OpenLayers.Renderer.SVG.prototype.drawFeature.apply(this, arguments);
			    },

			    /**
			     * Method: drawLineString
			     * Method which extends parent class by also drawing an arrow in the middle
			     * of the line to represent it's orientation.
			     */
			    drawLineString: function(node, geometry) {
			        this.drawArrows(geometry, node._style);
			        return OpenLayers.Renderer.SVG.prototype.drawLineString.apply(this, arguments);
			    }
			});

			OpenLayers.Renderer.prototype.removeArrows = function(geometry) {
			    var i;
			    // remove any arrow already drawn
			    // FIXME may be a performance issue
			    var children = this.vectorRoot.childNodes,
			        arrowsToRemove = [];
			    for (i = 0; i < children.length; i++) {
			        var child = children[i];
			        if (child.id.indexOf(geometry.id + "_arrow") != -1) {
			            arrowsToRemove.push(child);
			        }
			    }
			    for (i = 0; i < arrowsToRemove.length; i++) {
			        this.vectorRoot.removeChild(arrowsToRemove[i]);
			    }
			};

			OpenLayers.Renderer.prototype.drawArrows = function(geometry, style) {
			    var i;
			    if (style.orientation) {
					style.fillOpacity = 0.9; 								// NS01 Fill opacity (original = 1)
			        var pts = geometry.components;
			        var prevArrow,
			            distance;
			        for (i = 0, len = pts.length; i < len - 1; ++i) {
			            var prevVertex = pts[i];
			            var nextVertex = pts[i + 1];
			            var x = (prevVertex.x + nextVertex.x) / 2;
			            var y = (prevVertex.y + nextVertex.y) / 2;
			            var arrow = new OpenLayers.Geometry.Point(x, y);

			            arrow.id = geometry.id + '_arrow_' + i;
			            style = OpenLayers.Util.extend({}, style);
			            style.graphicName = "arrow";
			            style.pointRadius = 6; 								// NS01 Width of arrow (original = 4)
			            style.rotation = this.getOrientation(prevVertex, nextVertex);

			            if (prevArrow) {
			                var pt1 = this.map.getPixelFromLonLat(new OpenLayers.LonLat(arrow.x, arrow.y)),
			                    pt2 = this.map.getPixelFromLonLat(new OpenLayers.LonLat(prevArrow.x, prevArrow.y)),
			                    w = pt2.x - pt1.x,
			                    h = pt2.y - pt1.y;
			                distance = Math.sqrt(w*w + h*h);
			            }

			            // don't draw every arrow, ie. ensure that there is enough space
			            // between two
			            if (!prevArrow || distance > 100) { 				// NS01 Spacing (original = 40)
			                this.drawGeometry(arrow, style, arrow.id);
			                prevArrow = arrow;
			            }
			        }
			    }
			};

			OpenLayers.Renderer.prototype.getOrientation = function(pt1, pt2) {
			    var x = pt2.x - pt1.x;
			    var y = pt2.y - pt1.y;
			    var rad = Math.acos(y / Math.sqrt(x * x + y * y));
			    // negative or positive
			    var factor = x > 0 ? 1 : -1;
			    return Math.round(factor * rad * 180 / Math.PI);
			};

			//OpenLayers.Renderer.symbol.arrow = [0,2,  1,0, 2,2, 1,0, 0,2]; 				// NS01 Arrow styles
			  OpenLayers.Renderer.symbol.arrow = [1,4,  1,2, 0,2, 2,0, 4,2,  3,2, 3,4,  1,4 ];
			//OpenLayers.Renderer.symbol.arrow = [1,8,  1,3, 0,3, 3,0, 6,3,  5,3, 5,8,  1,8 ];
			//OpenLayers.Renderer.symbol.arrow = [4,16, 4,5, 0,5, 6,0, 12,5, 8,5, 8,16, 4,16];
		</script>

		<script type= "text/javascript">
			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Converts Easting/Northing pair to an OS National Grid Reference
			function myGetOSref(east, north) {
				var eX = east / 500000;
				var nX = north / 500000;
				var tmp = Math.floor(eX) - 5.0 * Math.floor(nX) + 17.0;
				eX = 20 - 5.0 * (Math.floor(north/100000)%5) + Math.floor(east/100000)%5
				if (eX  > 7.5) eX  = eX  + 1; 		// I is not used
				if (tmp > 7.5) tmp = tmp + 1; 		// I is not used

				var eing = east  - (Math.floor(east  / 100000)*100000);
				var ning = north - (Math.floor(north / 100000)*100000);
				var estr = (eing/10).toFixed(0); 	// Use 4 figure references
				var nstr = (ning/10).toFixed(0);
				while(estr.length < 4)
					estr = "0" + estr;
				while(nstr.length < 4)
					nstr = "0" + nstr;

				var ngr = String.fromCharCode(tmp + 65) + 
				          String.fromCharCode(eX  + 65) + 
				          " " + estr + " " + nstr;
				return ngr;
			}	

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Creates a mouse tracking coordinate overlay
			function myCoordinates(osMap) {
				// Create a position relative to top Right (1) and inset by the overlay size    
				screenCorner = new OpenSpace.Control.ControlPosition(1, new OpenLayers.Size(135, 10));
				// screenCorner = new OpenSpace.Control.ControlPosition(4, new OpenLayers.Size(6, 84)); // bottom left
				// Create the overlay for the coordinates and add it to the map    
			    screenOverlay = new OpenSpace.Layer.ScreenOverlay("coords");
   			    screenOverlay.setPosition(screenCorner);
			    osMap.addLayer(screenOverlay);
				// Create a grid projection to handle transform of the various points
				gridProjection = new OpenSpace.GridProjection();			    
				// Register any mouse movement and change the overlay coordinates based on this
				osMap.events.register("mousemove", osMap, function(e) {
					var pt = osMap.getMapPointFromViewPortPx(e.xy);
					var lonlat = gridProjection.getLonLatFromMapPoint(pt);
					var east  = pt.getEasting();
					var north = pt.getNorthing();
					screenOverlay.setHTML('<div style="padding: 0px 0px 2px 2px; width:122px; color:black; font-size:14px; line-height:15px; background-color:#e6e6e6;">'
						+ 'OS:  ' + myGetOSref(east, north) + '<br>'     
						+ 'E:   ' + east.toFixed(0)   	    + '<br>'
						+ 'N:   ' + north.toFixed(0) 	    + '<br>'
						+ 'Lng: ' + lonlat.lon.toFixed(8)   + '<br>'
						+ 'Lat: ' + lonlat.lat.toFixed(8)
						+ '</div>');
				});
/*
// Register mouse double click to add new marker
osMap.events.remove('dblclick');
osMap.events.register('dblclick', osMap, function(e) {
	var pt = osMap.getMapPointFromViewPortPx(e.xy);
	//alert(pt);
	var east  = pt.getEasting();
	var north = pt.getNorthing();	
	createMarker(pt.lon, pt.lat, 'marker_yellow', '30', '('+myGetOSref(east, north)+')');
});
*/
			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Converts waypoints to markers
			// NS01 NS02 NS03 NS04
			function myWaypoints(zIndex=999) {
			    // console.log('myWaypoints:');
				var meta = {!! json_encode($meta) !!};
				if (typeof(meta['wpt'])=='undefined') { j=0; } else { j=meta['wpt'].length; } 
				for(i=0; i<j; ++i) {
					wp = meta['wpt'][i];
	// console.log(wp);
					if (wp['icon']) { 											// NS02
						iconName = wp['icon']; 									// Icon was set in gpx wpt tag 
					} else { 													// NS02		
						if      (i==0) { iconName = 'marker_red';   }			// First Marker
						else if (i==j) { iconName = 'marker_green'; }			// Last Marker 
						else 		   { iconName = 'marker_blue';  }			// Intermediate Marker
					}
					iconSize = wp['iconsize'] ? wp['iconsize'] : '30'; 			// NS02
	// console.log(iconName);
					if(!wp['link' ]) { wp['link' ]=''; }
					if(!wp['image']) { wp['image']=''; }
					link  = wp['link' ].replace(/"/g, '').replace(/'/g, '');	// strip quotes
					image = wp['image'].replace(/"/g, '').replace(/'/g, '');	// strip quotes
					addMarker(wp['lon'], wp['lat'], iconName, iconSize, wp['name'], wp['osref'], link, image); // NS01 NS02
				}

				var markers = osMap.getMarkerLayer();					
				markers.setZIndex(zIndex);
			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Adds a marker to the marker layer with optional text, link, image
			// NS01 NS02
			function addMarker(lon, lat, iconName='marker_red', iconSize='30', text='', subText=false, link=false, image=false) {
	            // console.log('addMarker: '+lon+', '+lat+', '+iconName+', '+iconSize+', '+text+', '+link+', '+image);
				var pos        = new OpenSpace.MapPoint(lon, lat);
				var iconFile   = '/folders/icons/'+iconName+'.png';			// NS02 Point at our icon
				var size       = iconSize.split(","); 						// NS02
				var sizeW      = parseInt(size[0]);							// NS02
				var sizeH      = size[1] ? parseInt(size[1]) : sizeW+10; 	// NS02
				// console.log(sizeW, sizeH);
				var size       = new OpenLayers.Size(sizeW, sizeH);
				var offset     = new OpenLayers.Pixel(-(size.w/2), -size.h);
				var infoAnchor = new OpenLayers.Pixel(sizeW/3*2, 5);
				var icon       = new OpenSpace.Icon(iconFile, size, offset, null, infoAnchor);

				if (subText) { 												// NS01
					text = text+'<span style="color:grey;"> ('+subText+') </span>';
				}
				if (image) {
					var infoHtml = '';
					if (text) { infoHtml = infoHtml+'<p>'+text+'</p>';			}
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

				// console.log(infoHtml);
				marker = osMap.createMarker(pos, icon, infoHtml, infoSize);
				//marker = osMap.createMarker(pos, icon);

				//console.log(marker);
				//var infoWindow = new OpenSpace.InfoWindow('map', pos, infoSize, infoHtml, infoAnchor, true);
				//console.log(infoWindow);
			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Dynamically Creates a marker to the marker layer with optional text, link, image
			// NS01 NS02
			function createMarker(lon, lat, iconName='marker_red', iconSize='30', text='', subText=false, link=false, image=false) {
	            // console.log('addMarker: '+lon+', '+lat+', '+iconName+', '+iconSize+', '+text+', '+link+', '+image);
				var pos        = new OpenSpace.MapPoint(lon, lat);
				var iconFile   = '/folders/icons/'+iconName+'.png';			// NS02 Point at our icon
				var size       = iconSize.split(","); 						// NS02
				var sizeW      = parseInt(size[0]);							// NS02
				var sizeH      = size[1] ? parseInt(size[1]) : sizeW+10; 	// NS02
				// console.log(sizeW, sizeH);
				var size       = new OpenLayers.Size(sizeW, sizeH);
				var offset     = new OpenLayers.Pixel(-(size.w/2), -size.h);
				var infoAnchor = new OpenLayers.Pixel(sizeW/3*2, 5);
				var icon       = new OpenSpace.Icon(iconFile, size, offset, null, infoAnchor);

				if (subText) { 												// NS01
					text = text+'<span style="color:grey;"> ('+subText+') </span>';
				}
				if (image) {
					var infoHtml = '';
					if (text) { infoHtml = infoHtml+'<p>'+text+'</p>';			}
					if (link) { infoHtml = '<a href="'+link+'">'+infoHtml+'</a>'; }
					infoHtml = infoHtml+'<a href="'+image+'"><img src='+image+'?t=200'+' style="max-width: 200px; max-height:100px; width:auto; height:auto;);"/></a>';
					var infoSize = new OpenLayers.Size(220, 210);
				} else {
					var infoHtml = '<p>'+text;
					if (link) {
						infoHtml = infoHtml+'<a href="'+link+'"><span class="fas fa-link ml-2"></span></a>';
					}
					infoHtml = infoHtml+'</p>';
					infoHtml = infoHtml+'<form><input type="text"></form>';
					var infoSize = new OpenLayers.Size(450, 250);
				}

				// console.log(infoHtml);
				marker = osMap.createMarker(pos, icon, infoHtml, infoSize);
				console.log(marker);

			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// Add/Update/Remove a GPX track layer 
			function myAddLayer($this, name, style=false, fgpx) {
				myrc = false;
				if (style) { 												// Add/Update
			    	if (name in $this.layers) {
			    		osMap.removeLayer($this.layers[name]);
					}
					$this.layers[name] = new OpenLayers.Layer.GML(name, fgpx, {
						format: OpenLayers.Format.GPX,
					    style: $this.styles[style], 
					    projection: new OpenLayers.Projection('EPSG:4326'), //4326
						renderers: ['SVGExtended']							// Add direction arrows if style orientation: true
			        });
			   		osMap.addLayer($this.layers[name]);
			   		myrc = $this.layers[name];
			    } else { 													// Remove
			    	if (name in $this.layers) {
			    		osMap.removeLayer($this.layers[name]);
			    		var i = indexOf(name);
			    		$this.layers = $this.layers.splice(i, 1);
			    	}
			    }
				return myrc;
			}

			// ========================================================================== //
			// Handles Ordnance Survey maps
			// initmapbuilder(divID, [file.gpx])
			// eg initMapBuilder('map', "{{ route('private.getFile', [$file->id]) }}");
			// NS01 
 			function initMapBuilder(id, fgpx=false) {
	            //console.log('initMapBuilder: '+id);
				//var fgpx = "{{ route('private.getFile', [$file->id]) }}"
				var options = {	resolutions: [500, 200, 100, 50, 25, 10, 5, 4, 2.5, 2, 1] };
				osMap = new OpenSpace.Map(id, options);
				setglobaloptions();
				//setmapbuilderoptions();
				//makegrid();								// Adds Cursor Tracking box
				myCoordinates(osMap);						// Adds Cursor Tracking box (personalised)		
				addSearchBox(1);							// Adds Search Box
				var scaleline = new OpenLayers.Control.ScaleLine({ 
					div: document.getElementById("OSscaleline"),
					maxWidth: 200,
				});
				osMap.addControl(scaleline);
				var scale = new OpenLayers.Control.Scale();
				scale.div = document.getElementById("OSscale");
				osMap.addControl(scale); 					// Ads Scales

				var myLayers = { 
					styles:{
						base:   {strokeColor: "black", strokeWidth: 1,  strokeOpacity: 0.90, orientation: false},
						arrows: {strokeColor: "black", strokeWidth: 1,  strokeOpacity: 0.90, orientation: true },
						blue:   {strokeColor: "blue",  strokeWidth: 6,  strokeOpacity: 0.55, orientation: false},
						blue2:  {strokeColor: "blue",  strokeWidth: 8,  strokeOpacity: 0.50, orientation: false},
						green:  {strokeColor: "green", strokeWidth: 10, strokeOpacity: 0.90, orientation: false},
						red:    {strokeColor: "red",   strokeWidth: 5,  strokeOpacity: 0.55, orientation: false}
					},
					layers:{} 								// Track our own layers here see myAddLayer()
				}	

				if (fgpx) {	 											// Load GPX file
					lgpx1 = myAddLayer(myLayers, 'track1', 'blue', fgpx);
					osMap.zoomToExtent(lgpx1.getDataExtent());
					osMap.events.register("zoomend", osMap, function() { // HERE WHEN EACH ZOOM ENDS
						zoom = this.getZoom();
						if (zoom<1) { 									// Use a fixed centre for UK map
							this.setCenter(new OpenSpace.MapPoint(405000, 240000), 0);
						}
						if 		(zoom==6) { style1 = 'blue2'; style2 = 'arrows'; }
						else if (zoom<=4) { style1 = 'green'; style2 = 'base';   }
						else 			  { style1 = 'blue';  style2 = 'arrows'; }
						l = myAddLayer(myLayers, 'track1', style1, fgpx);
						l = myAddLayer(myLayers, 'track2', style2, fgpx);

						var markers = osMap.getMarkerLayer(); 			// Reset marker zIndex above new layer					
						markers.setZIndex(+l.getZIndex() + 10);
					});

					lgpx1.events.register("loadend", lgpx1, function() { // HERE WHEN LOAD END
						this.map.zoomToExtent(this.getDataExtent());
						myWaypoints(+lgpx1.getZIndex() + 10); 			// NS01 Set Waypoint markers to TOP 
					});
				} else {												// Just show UK map
					osMap.setCenter(new OpenSpace.MapPoint(405000, 240000), 0);
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
	@endif

@endsection
