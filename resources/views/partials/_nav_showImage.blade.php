@if ($data[7]>0)
	<div class="row mt-3 justify-content-center">
		<div class="col-sm-12 text-center">
			{{-- Items navigation buttons: First, Previous, Stop, autoPlay, Next, Last --}}
			@for ($i=0; $i < 6; $i++)
				@if ($icons[$i]!='')
					@if (is_array($icons[$i])) 		{{-- autoPlay buttons --}}
						<a href="#" id="playButton" data-data="{{ json_encode($data) }}"
							class="btn btn-outline-dark {{ $data[$i] ?: 'disabled' }}"
							onClick="myTogglePlayButton(event)">
							<i class="{{ $icons[$i][$data[10]] }}" data-icons="{{ json_encode($icons[$i]) }}"></i>
						</a>
					@else 							{{-- All other buttons --}}
						<a href="{{ route('files.show', $data[$i]) }}"
							class="btn btn-outline-dark {{ $data[$i] ?: 'disabled' }}"
							onClick="myVolume()">
							<i class="{{ $icons[$i] }}"></i>
						</a>
					@endif
				@endif 
			@endfor
		</div>

		{{-- Items navigation progress bar: Min, Max, Pos, Index --}}
		<div class="col-sm-12 text-center" style="margin-top:-12px;">
			<input type="range" class="custom-range" min="{{ $data[6] }}" max="{{ $data[7] }}" value="{{ $data[8] }}"
			 onChange="this.disabled=true; myRange(this.value, {{ $data[9] }})"
			 data-toggle="tooltip" data-placement="bottom" title="{{ $data[8]+1 }} of {{ $data[7]+1 }}">
		</div>
	</div>
	
	<script>
		// Toggle PlayButton
		function myTogglePlayButton(e) {
			e.preventDefault();
			var el=e.target;
			var classes=el.dataset.icons;
			myToggleClasses(el, classes);
			var id=myPlayList('button');
			if (id) { myFetch(id); }
			}

		// Toggle classes
		function myToggleClasses(el, classes) {
			classes=JSON.parse(classes);
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

		// Select next item to automatically play
		function myPlayList(op=false) {
			var el  =document.getElementById('playButton');
			var data=JSON.parse(el.dataset.data);
			var ids =JSON.parse(data[9]);
			var id  =ids[data[8]];
			var pl  =0;
			el=el.children[0];
			if      (el.classList.contains('f-playback')) { pl=3; id=data[1];						   }
			else if (el.classList.contains('f-loopback')) { pl=4; id=data[1] ? data[1] : ids[data[7]]; }
			else if (el.classList.contains('f-play'    )) { pl=1; id=data[4];						   }
			else if (el.classList.contains('f-loop'    )) { pl=2; id=data[4] ? data[4] : ids[data[6]]; }
			//else if (el.classList.contains('f-pause'   )) { pl=0; id=ids[data[8]]; 					   }
			//console.log(op, pl, 'id='+id);
			if      (op=='button'      ) { return pl==0 ? ids[data[8]] : ids[data[8]]+'?pl='+pl; }
			else if (pl!=0 && id!=false) { myFetch(id+'?pl='+pl); }
		}   
		
		// Get the item's id from its index in the list and fetch it   
		function myRange(index, list=[]) {
			if (index in list) {
				myFetch(list[index]);			// Go fetch the new page
			}
		}
		function myFetch(id) {
			console.log('myFetch: '+id);
			myVolume();
			var url="{{ route('files.show', '_id_') }}";
var url="{{ route('files.showFile', [$file->id, substr($file->mime_type, 0, 2) == 'im' ? 'r=y' : '']) }}"
			url=url.replace('_id_', id);
			window.location.href=url; 			// Go fetch the new page
		}
	</script>
@endif
