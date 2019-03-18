@extends('manage')

@section('title','| Manage Test Create')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="" id=""><span class="fas fa-flask mr-4">
				</span>Test Create with preview and progress bars</a></h1>
			<hr>
			{{ Form::model($file, ['route' => ['tests.store'], 'files' => true, 'id' => 'ul1-form']) }}
			    {{-- This section is used for File Upload with preview (Form) ------------------------------------------------}}
				<div class="progress mt-2" style="background-color: white;">
					<div id="ul1-progressu" class="progress-bar" role="progressbar" style="width:0%;">0%</div>
				</div>	
				{{ Form::label('', 'Files:', ['class'=>'font-bold xform-spacing-top']) }}
				<span class="author-time">Good to drag & drop</span>
				<div class="row ml-auto">
					<div id="ul1-files" class="col-md-9 custom-file">
						{{ Form::file('files[]', ['class'=>'form-control custom-file-input', 'accept' => $mimes, 'multiple' => '', 'onChange' => 'myUpload(this)']) }} 
						{{ Form::label('files', 'Initialising...', ['class' => 'custom-file-label']) }}
					</div>
					<div id="ul1-delete" class="col-md-3 myFile-img-delete" style="display:none;">
						{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete Files', ['class'=>'btn btn-outline-danger btn-block mb0', 'onClick' => 'myUpload(this)']) }}
						{{ Form::checkbox('files_delete', 'delete', false, ['hidden']) }}
					</div>
					<div id="ul1-reset"  class="col-md-3 myFile-img-reset"  style="display:none;">
						{{ Form::button('<i class="fas fa-sync-alt mr-2"></i>Reset Files', ['class'=>'btn btn-outline-dark btn-block', 'onClick' => 'myUpload(this)']) }}
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 mt-2">
						{{ Form::button('<i class="fas fa-times-circle mr-2"></i>NO Cancel', ['class'=>'btn btn-outline-danger btn-block', 'onclick'=>'window.history.back(); event.preventDefault ?
						event.preventDefault : event.returnValue=false;']) }}
					</div>				
					<div id="ul1-submit" class="col-md-6 mt-2" route="{{ route('tests.upload') }}">
						{{ Form::button('<i class="fas fa-plus-circle mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
			    {{-- This section is used for File Upload with preview (Form) ------------------------------------------------}}
			{{ Form::close() }}
		</div>

		<div class="col-md-4 myWrap mt-5">
			<hr>
		    {{-- This section is used for File Upload with preview (Preview) -------------------------------------------------}}
			<div id="ul1-old" class="mt-2">
				@if (isset($oldfile))
					{{-- Any file from controller will be rendered here --}}
					<img width="100%" src="{{ route('private.getFile', [$oldfile->id, 'r=n&t=400']) }}" />
				@endif
@if (isset($oldfiles))
					<div class="progress mt-2 mb-2" style="background-color: white;">
						<div id="dl1-progressu" class="progress-bar" role="progressbar" style="width:0%;">0%</div>
					</div>
					<div id="dl1-carousel" class="carousel slide" data-ride="carousel" data-files="{{ $oldfiles }}" width="400">
						<div class="carousel-inner image-crop-height" style="--croph:35vw">
							{{-- Uploading files will be rendered here --}}
@foreach ($oldfiles as $file)
{{--
	<div class="carousel-item {{ $loop->first ? 'active' : '' }}">
		<img width="100%" src="{{ route('private.getFile', [$file->id, 'r=n&t=400']) }}" />
	</div>
--}}
@endforeach
						</div>
						<div id="dl1-controls">
							<a class="carousel-control-prev" href="#dl1-carousel" role="button" data-slide="prev">
							    <span class="carousel-control-prev-icon"></span>
							    <span class="sr-only">Previous</span>
							</a>
							<a class="carousel-control-next" href="#dl1-carousel" role="button" data-slide="next">
							    <span class="carousel-control-next-icon"></span>
							    <span class="sr-only">Next</span>
							</a>
						</div>	
					</div>
				@endif
			</div>				
			<div class="progress mt-2 mb-2" style="background-color: white;">
				<div id="ul1-progressr" class="progress-bar" role="progressbar" style="width:0%;">0%</div>
			</div>
			<div>
				<img id="ul1-preview" src="#" alt="" width="400" hidden />
			</div>
			<div>
				<img id="ul1-thumb" src="#" alt="" width="400" hidden />
			</div>
			<div id="ul1-carousel" class="carousel slide" data-ride="carousel" width="400">
				<div class="carousel-inner image-crop-height" style="--croph:35vw">
					{{-- Uploading files will be rendered here --}}
				</div>
				<div id="ul1-controls">
					<a class="carousel-control-prev" href="#ul1-carousel" role="button" data-slide="prev">
					    <span class="carousel-control-prev-icon"></span>
					    <span class="sr-only">Previous</span>
					</a>
					<a class="carousel-control-next" href="#ul1-carousel" role="button" data-slide="next">
					    <span class="carousel-control-next-icon"></span>
					    <span class="sr-only">Next</span>
					</a>
				</div>	
			</div>
		    {{-- This section is used for File Upload with preview (Preview) -------------------------------------------------}}
		</div>
			
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/helpers.js')	 	  !!}
	{!! Html::script('js/jsmediatags.min.js') !!}

	<script>
		myUploadInit   (['ul1', 'ul2', 'ul3']); 	// List of prefix-ids - one for each files upload   section
		myShowFilesInit(['dl1']);		 			// List of prefix-ids - one for each files download section

		// Function flow: myUpload->myReadFile->(image:myResizeImage)(audio:myGetPoster)->myAddToCarousel
		// Each files upload controls set has its unique id prefix saved in $uls[]
		// At each interrupt $ul should be loaded from $uls with the correct id set 
		function myUploadInit(ids) {
			//_T(); // Trace
			if (typeof($uls)=='undefined') { $uls=[]; }
			ids.forEach(function(id) {
				var files=_(id+'-files');
				if (files) { 									// Only initialise if xxx-files exists 
					$ul=[];
					$ul['id'		  ]=id;
					$ul['elForm'	  ]=_(id+'-form');
					$ul['elOld'		  ]=_(id+'-old');
					$ul['elReset'	  ]=_(id+'-reset');
					$ul['elDelete'	  ]=_(id+'-delete');
					$ul['elProgressR' ]=_(id+'-progressr');
					$ul['elProgressU' ]=_(id+'-progressu');
					$ul['elPreview'	  ]=_(id+'-preview');
					$ul['elThumb'	  ]=_(id+'-thumb');
					$ul['elCarousel'  ]=_(id+'-carousel');
					$ul['elControls'  ]=_(id+'-controls');
					$ul['elSubmit'    ]=_(id+'-submit');
					$ul['elFiles'	  ]=files.childNodes[1]; 
					$ul['elLabel'	  ]=files.childNodes[3];
					$ul['old'		  ]=$ul['elOld'   ].childNodes[1];
					$ul['elDeleteCbox']=$ul['elDelete'].childNodes[3];
					$ul['allowedImage']=/(\.jpg|\.jpeg|\.png|\.gif|\.jpe|\.bmp|\.ico)$/i;
					$ul['allowedVideo']=/(\.mp4|\.mov|\.avi|\.mkv|\.mpg|\.mts|\.flv|\.webm)$/i;
					$ul['allowedAudio']=/(\.mp3|\.wav|\.flac|\.wma|\.m4a)$/i;
					$uls[id]=$ul;								// Save the new id set
					$ul['elReset'].childNodes[1].click(); 		// Simulate Reset pressed
					myTransferUp('init', $ul, false);					
				}	
			});
		}	

		function myUpload(e) {
			//_T(); // Trace
			var data, i;
			data=e.parentNode.id.split('-');
			$ul=$uls[data[0]]; 									// Retrieve correct id array
			$ul['op']=data[1];									// Save interrupting operation type 
			if ($ul['op']=='reset') {
				if($ul['uploadClicked']) { location.reload(); } // Force Page refresh which should include any flash messages 
				myHideShowElement([
					$ul['elProgressR'],
					$ul['elProgressU'],
					$ul['elPreview'  ],
					$ul['elThumb'	 ],
					$ul['elCarousel' ],
					$ul['elReset'	 ],
					$ul['elDelete'	 ],
					$ul['elOld'		 ]
				]);
				if ($ul['old']) {
					myHideShowElement([$ul['elOld'], $ul['elDelete'], $ul['elSubmit']], 'block'); 		// Show
				}
				$ul['elDeleteCbox'].checked=false;
				$ul['elFiles'].value='';																// Clear buffer
				$ul['elLabel'].innerHTML='Select files to upload...';
			} else if ($ul['op']=='delete') {
				myHideShowElement([$ul['elDelete'], $ul['elOld']]); 									// Hide
				myHideShowElement([$ul['elReset']], 'block'); 											// Show
				$ul['elDeleteCbox'].checked=true;
				$ul['elLabel'].innerHTML='<p class="text-danger"><i class="fas fa-trash-alt mr-2"></i>File will be Deleted.</p>';
			} else if ($ul['op']=='submit') {
				var route, formdata, request;
				myHideShowElement([$ul['elSubmit'],$ul['elDelete']]);				 					// Hide
				myHideShowElement([$ul['elReset'], $ul['elProgressU']], 'block');						// Show
				route=e.parentNode.getAttribute('route');							// Get route from submit button's div
				request=new XMLHttpRequest();
				formdata=new FormData($ul['elForm']);
				request.upload.addEventListener('progress', myTransferUp.bind(null, 'progress', $ul), false);
				request.addEventListener('load', myTransferUp.bind(null, 'complete', $ul), false);
				request.open('post', route);
				request.send(formdata);
				$ul['uploadClicked']=true; 											// Used by reset to Refresh Page
			} else {
				if (e.files && e.files[0]) {
					$ul['elDeleteCbox'].checked=false;
					$ul['poster']=false;
					delete $ul['slideCount'];				
					$ul['count']=e.files.length;
					$ul['toDo' ]=$ul['count'];
					$ul['elLabel'].innerHTML=$ul['count']==1 ? $ul['count']+' file selected.' : $ul['count']+' files selected.';
					for (i=0; i<$ul['count']; ++i) {
						myReadFile(e.files[i], $ul);
					}
					myHideShowElement([$ul['elDelete'], $ul['elOld']]);										// Hide
					myHideShowElement([$ul['elProgressR'], $ul['elCarousel'], $ul['elReset']], 'block');	// Show
				}
			}	
		}

		// Read the local files, call carousel to add an appropriate preview image and display the progress bar (Read) 
		function myReadFile(file, $ul) {
			//_T(); // Trace
			var maxLength, msg, part, reader;
	    	maxLength=20;
    		msg=file.name;
    		if (msg.length>maxLength+3) {										// Trim for Label
    			part=parseInt(maxLength/2);
    			msg=msg.substr(0, part) + '...' + msg.substr(msg.length-part, part);
	    	}
	    	file.displayName=msg;	

			reader=new FileReader();
			reader.onload=(function($ul) {
				return function(e) {
					//_T('Reader.onload'); // Trace
					var item, percent, color;
					if ($ul['allowedAudio'].exec(file.name)) {
						myGetPoster(file)
							.then(function whenOK(image) { $ul['poster']=image; 					   })
							.catch(function notOK() 	 { $ul['poster']="{{ asset('favicon.ico') }}"; })
							.finally(function() {
								item='<video class="d-block w-100" controls="" '+
									'src="'+e.target.result+'" poster="'+$ul['poster']+
									'" onPlay="myVideo(this, \'play\')" onPause="myVideo(this, \'pause\')" /></video>'; 
								myAddToCarousel(item, file.displayName, $ul); 			// Display audio with image
							});
					} else if ($ul['allowedVideo'].exec(file.name)) {
						item='<video class="d-block w-100" muted="" controls="" loop="" '+
							'onPlay="myVideo(this, \'play\')" onPause="myVideo(this, \'pause\')" >'+
							'<source src="'+e.target.result+'" type="video/mp4" />'+
							'<img src="'+"{{ asset('favicon.ico') }}"+'" /></video>';
						myAddToCarousel(item, file.displayName, $ul); 					// Display video				
					} else if ($ul['allowedImage'].exec(file.name)) {
						myResizeImageTo(e.target.result, file, $ul); 	 				// Display scaled image 					
					} else {
			        	myAddToCarousel(false, file.displayName, $ul); 	 				// Display placeholder					
					}

					--$ul['toDo']; 														// Progress bar (Read)	
					percent=Math.round(($ul['count']-$ul['toDo'])/$ul['count']*100);
					if (percent>=100) {	color='green';	} else { color='blue'; }
					$ul['elProgressR'].setAttribute('aria-valuenow', percent);
					$ul['elProgressR'].style.width=percent+'%';
					$ul['elProgressR'].innerHTML=percent+'% Read';
					$ul['elProgressR'].style.backgroundColor=color;
				};
			})($ul);

			reader.readAsDataURL(file);	
		}

		function myAddToCarousel(item, title, $ul) {
			//_T(); // Trace
			var active, htmlString;
			if (typeof($ul['slideCount'])=='undefined') {
				$ul['elCarousel'].firstElementChild.innerHTML='';	// Clear carousel contents
				$ul['slideCount']=1;								// Reset slide counter to 1
				active=' active';									// Make 1st. item active
				myHideShowElement([$ul['elControls']]);				// Hide previous/next controls
			} else {
				++$ul['slideCount'];
				active='';
				myHideShowElement([$ul['elControls']], 'block');	// Show previous/next if more than one item
			}
			if (! item) { 											// If no item, use a default place holder
				item='<img class="d-block w-100" src="'+"{{ asset('favicon.ico') }}"+'" />'; 
			}
			htmlString=
				'<div class="carousel-item'+active+'">'+item+
				'<div class="carousel-caption d-none d-md-block">'+
				'<p><h5><span class="carousel-caption-inner">'+
				$ul['slideCount']+': '+title+'</span></h5></p></div></div>';
			$ul['elCarousel'].firstElementChild.insertAdjacentHTML('beforeend', htmlString);
		}	

		// Asynchronous promise to find & return MetaTag:picture
		var myGetPoster=function(file) {
			//_T(); // Trace
			return new Promise(function(resolve, reject) {
				jsmediatags.read(file, {
			        onSuccess: function(tag) {
			        	var tags, image, base64String, i
			        	tags=tag.tags;
			        //	console.log(tags);
						image=tags.picture;
			          	if (image) {
				            base64String='';
				            for (i=0; i<image.data.length; ++i) {
				                base64String+=String.fromCharCode(image.data[i]);
				            }
				            resolve("data:image/jpeg;base64,"+window.btoa(base64String)); 	// Return on Good Picture
				        } else { reject(false); }   										// Return on Missing
			        },
			        onError: function() { reject(false); } 									// Return on Error	
			    });
			});
		}	    

		// For resize to work, you must call with a width OR set a width on the $ul['elCarousel'] <img> tag
		function myResizeImageTo(data, file, $ul, newWidth) {
			//_T(); // Trace
			var image;
			image=new Image();
			image.src=data;
			image.onload=function() {
				//_T('Image.onload'); // Trace
				var newHeight, canvas, ctx;
				if (typeof(newWidth)=='undefined') { newWidth=$ul['elCarousel'].getAttribute('width'); }
				if (typeof(newWidth)=='undefined') { newWidth=image.width; }
				newHeight=Math.floor(image.height/image.width*newWidth);
	       		canvas=document.createElement('canvas');
	       		canvas.width=newWidth;
	       		canvas.height=newHeight;
	        	ctx=canvas.getContext('2d');
	        	ctx.drawImage(image, 0, 0, newWidth, newHeight);
		        dataURI=canvas.toDataURL(file.type, 0.92);
		        //$ul['elCarousel'].src=dataURI;
		        myAddToCarousel('<img class="d-block w-100" src="'+dataURI+'" />', file.displayName, $ul);						
			}
			image.onError=function() {
		        myAddToCarousel(false, file.displayName, $ul); 	// Just add a placeholder to carousel 					
			}
		}	

		// Function flow: myShowFilesInit->myShowFiles->myTransferDown->(myTransferDownRetry)->myAddToCarousel
		// Each files upload controls set has its unique id prefix saved in $dls[]
		// At each interrupt $dl should be loaded from $dls with the correct id set 
		function myShowFilesInit(ids) {
			_T(); // Trace
			if (typeof($dls)=='undefined') { $dls=[]; }
			ids.forEach(function(id) {
				var files=_(id+'-carousel');
				if (files) { 									// Only initialise if xxx-files exists 
					$dl=[];
					$dl['id'		  ]=id;
					$dl['elCarousel'  ]=_(id+'-carousel');
					$dl['files'  	  ]=$dl['elCarousel'].getAttribute('data-files');
					$dl['elControls'  ]=_(id+'-controls');
					$dl['elProgressU' ]=_(id+'-progressu');
					$dl['allowedImage']=/(\.jpg|\.jpeg|\.png|\.gif|\.jpe|\.bmp|\.ico)$/i;
					$dl['allowedVideo']=/(\.mp4|\.mov|\.avi|\.mkv|\.mpg|\.mts|\.flv|\.webm)$/i;
					$dl['allowedAudio']=/(\.mp3|\.wav|\.flac|\.wma|\.m4a)$/i;
					$dl['loaded'	  ]={urls: [], progress: 0, total: 0};
					$dl['routeSmall'  ]="{!! route('private.getFile', [':id', 'r=n&t=400']) !!}";
					$dl['routeMedium' ]="{!! route('private.getFile', [':id', 'r=n&t=800']) !!}";
					$dl['routeLarge'  ]="{!! route('private.getFile', [':id', 'r=y'		 ]) !!}";
					$dls[id]=$dl;								// Save the new id set
					myShowFilesCarousel($dl);					
				}	
			});
		}	

		function myShowFilesCarousel($dl) {
			_T(); // Trace
			var files=JSON.parse($dl['files']);
			$dl['loaded']={urls: [], progress: 0, total: 0, count: files.length};	// Reset tracking data
			for (file of files) {
		    	file.style='carousel';
				maxLength=20;
				msg=file.title;
	    		if (msg.length>maxLength+3) {											// Trim for Label
	    			part=parseInt(maxLength/2);
	    			msg=msg.substr(0, part)+'...'+msg.substr(msg.length-part, part);
		    	}
		    	file.displayName=msg;
				url=$dl['routeSmall'];
				url=url.replace(':id', file.id);
				request=new XMLHttpRequest();
				request.responseType="arraybuffer";
				request.addEventListener('progress', myTransferDown.bind(null, 'progress', file, $dl), false);
				request.addEventListener('load', 	 myTransferDown.bind(null, 'complete', file, $dl), false);			
				request.open('get', url);
				request.send();
file.$id=$dl['id'];file.$url=url;myGetFile(file);
			}
		}

//============================================================================================================================
		function myGetFile(file) {
			_T(); // Trace
			request=new XMLHttpRequest();
			request.responseType="arraybuffer";
			request.addEventListener('progress', myGetFileI.bind(null, 'progress', file), false);
			request.addEventListener('load', 	 myGetFileI.bind(null, 'complete', file), false);			
			request.open('get', file.$url);
			request.send();			
		}
		function myGetFileI(op, file, e) {
			_T(); // Trace
			var $dl, urls, progress, total, count;
		console.log('myGetFileInterrupt: '+op+' '+file.$id+' '+file.$url);
			if (e.target.status==500 && ! file.$status==500) { 		// Server error - retry once 
				file.$status=500;
				myGetFile(file);
				return;
			}

			$dl 	=$dls[file.$id];
			urls    =$dl['loaded']['urls'    ];
			progress=$dl['loaded']['progress'];
			total   =$dl['loaded']['total'   ];
			count   =$dl['loaded']['count'   ];

			size=parseFloat(e.target.getResponseHeader('x-size'));	// x-size must be set in the response header @controller
			if (typeof(size)=='number') {
				if (! urls.includes(e.target.responseURL)) {
					urls.push(e.target.responseURL);
					total=total+size;
				} 	
           	}

			if (op=='progress') { 									// Transfer Progress
				$dl['loaded']={urls: urls, progress: progress, total: total, count: count};
				progress=progress+e.loaded;
	        } else if (op=='complete') { 							// Transfer Complete
				progress=progress+e.loaded;
				$dl['loaded']={urls: urls, progress: progress, total: total, count: count};
			}
        	percent=(progress/total*100|0);
			$dls[file.$id]=$dl;
		console.log('myGetFileInterrupt: '+op+' '+file.$id+' '+file.$url+' '+percent);
		}			
//================================================================================================================================


		function myShowFileMedium(id, $dl) {
			_T(); // Trace
			var files, file, request, msg, url;
			files=JSON.parse($dl['files']);
			for (file of files) {
				if (file.id==id) { break; }
			};

	    	file.style='medium';
			maxLength=20;
			msg=file.title;
    		if (msg.length>maxLength+3) {											// Trim for Label
    			part=parseInt(maxLength/2);
    			msg=msg.substr(0, part)+'...'+msg.substr(msg.length-part, part);
	    	}
	    	file.displayName=msg;
			url=$dl['routeMedium'];
			url=url.replace(':id', file.id);
			request=new XMLHttpRequest();
			request.responseType="arraybuffer";
			request.addEventListener('progress', myTransferDown.bind(null, 'progress', file, $dl), false);
			request.addEventListener('load', 	 myTransferDown.bind(null, 'complete', file, $dl), false);			
			request.open('get', url);
			request.send();
		}	

		function myShowFile(file, style, $dl) {
			_T(); // Trace
			var files, f, request, msg, url;
			if (Number.isInteger(file)) {
				files=JSON.parse($dl['files']);
				for (f of files) {
					if (f.id==file) { file=f; break; }
				};
			}	
			if (style=='large'   ) {
				url=$dl['routeMedium'];
				maxLength=80;
			} else if (style=='medium'  ) {
				url=$dl['routeMedium'];
				maxLength=40;


			} else if (style=='lightbox') {
				url=$dl['routeSmall'];
				maxLength=16;
			} else {
				url=$dl['routeSmall'];
				maxLength=20;
			}
    		msg=file.title;
    		if (msg.length>maxLength+3) {										// Trim for Label
    			part=parseInt(maxLength/2);
    			msg=msg.substr(0, part)+'...'+msg.substr(msg.length-part, part);
	    	}
	    	file.displayName=msg;
			url=url.replace(':id', file.id);
			request=new XMLHttpRequest();
			request.responseType="arraybuffer";
		//  request.addEventListener('readystatechange', myTransferDown.bind(null, 'progress', file, style, $dl), false);
			request.addEventListener('progress', 		 myTransferDown.bind(null, 'progress', file, style, $dl), false);
			request.addEventListener('load', 			 myTransferDown.bind(null, 'complete', file, style, $dl), false);			
			request.open('get', url);
			request.send();
		}	

		// Handles progress events for data transfer Down from the server
		// request.addEventListener('readystatechange', myTransferDown.bind(null, 'progress', $dl), false);
		// request.addEventListener('progress', 		myTransferDown.bind(null, 'progress', $dl), false);
		// request.addEventListener('load', 			myTransferDown.bind(null, 'complete', $dl), false);
		// @controller $response->header("x-size", strlen($file));
    	// console.log(e.target.responseURL);
    	// console.log(e.target.readyState);
    	// console.log(e.loaded);
    	// console.log(e.target.getResponseHeader('x-size'));		
		function myTransferDown(op, file, $dl, e) {
			_T(op); // Trace
			var size, urls, progress, total, count, percent, color, msg, item;
			urls    =$dl['loaded']['urls'    ];
			progress=$dl['loaded']['progress'];
			total   =$dl['loaded']['total'   ];
			count   =$dl['loaded']['count'   ];
			size=parseFloat(e.target.getResponseHeader('x-size'));	// x-size must be set in the response header @controller
		//  console.log('op='+op+' loaded='+e.loaded+' size='+size+' type='+typeof(size)+' code='+e.target.status);
			if (e.target.status==500) {
				myTransferDownRetry(file, e.target.responseURL, $dl);
				return;
			}

			if (typeof(size)=='number') {
				if (! urls.includes(e.target.responseURL)) {
					urls.push(e.target.responseURL);
					total=total+size;
				} 	
           	}

			if (op=='progress') { 									// Transfer Progress
				$dl['loaded']={urls: urls, progress: progress, total: total, count: count};
				progress=progress+e.loaded;
	        } else if (op=='complete') {
				console.log(op, file.style);
				progress=progress+e.loaded;
				$dl['loaded']={urls: urls, progress: progress, total: total, count: count};
				if ($dl['allowedImage'].exec(file.file)) {
					var arrayBufferView=new Uint8Array(e.target.response);
					var blob=new Blob([arrayBufferView], { type: "image/jpeg" });
					var urlCreator=window.URL||window.webkitURL;
					var image=urlCreator.createObjectURL(blob);
if (file.style=='medium'){
					item='<img src="'+image+'" width="100%" onClick="myShowFileLarge('+file.id+', $dl)" />';
var w = 480, h = 340;

var popW = 800, popH = 700;
var leftPos = (w-popW)/2;
var topPos = (h-popH)/2;					
msgWindow = window.open('','popup', 'scrollbars=yes');

msgWindow.document.write(item);
					//myPopUp(item, file.displayName, $dl);	 // Display image
} else {
					item='<img src="'+image+'" width="100%" onClick="myShowFileMedium('+file.id+', $dl)" />';
					myAddToCarousel(item, file.displayName, $dl);	 // Display image			
}
				}			           	
	        }	
        	percent=(progress/total*100|0);
		//  msg=progress+' ('+percent+'%) loaded of '+total+' '+urls.length+' files of '+count;
		//  console.log(msg);        	
			if (percent>=100 && urls.length==count) { color='green'; msg=' ('+urls.length+' files)'; } else { color='blue'; msg=''; }
			$dl['elProgressU'].setAttribute('aria-valuenow', percent);
			$dl['elProgressU'].style.width=percent+'%';
			$dl['elProgressU'].innerHTML=percent+'% Downloaded'+msg;
			$dl['elProgressU'].style.backgroundColor=color;
		}

		// Only used if code=500 returned - may cause a loop !!
		function myTransferDownRetry(file, url, $dl) {
			_T(); // Trace
			var request;
			request=new XMLHttpRequest();
			request.responseType="arraybuffer";
		//	request.addEventListener('readystatechange', myTransferDown.bind(null, 'progress', $dl, file), false);
			request.addEventListener('progress', 		 myTransferDown.bind(null, 'progress', $dl, file), false);			
			request.addEventListener('load', 			 myTransferDown.bind(null, 'complete', $dl, file), false);
			request.open('get', url);
			request.send();
		}

		function myVideo(e, op) {
			_T(op); // Trace
			var elCaption=e.closest('div').childNodes[1].childNodes[1];
		//	console.log(elCaption);
			myHideShowElement([elCaption], op=='play' ? '' : 'block'); 			// Hide/Show
		}	

		// Handles initialisation and progress events for data transfer Up to the server
		// myTransferUp('init', $ul, false);
		// request.upload.addEventListener('progress', myTransferUp.bind(null, 'progress', $ul), false);
		// request.addEventListener('load', myTransferUp.bind(null, 'complete', $ul), false);
		function myTransferUp(op, $ul, e) {
			//_T(op); // Trace
			if (op=='init') { 										// Transfer Init
				$ul['elForm'].addEventListener('submit', function(e) {
					e.preventDefault();
					myUpload($ul['elSubmit'].childNodes[1]);
				}); 
			}
			else {
				var percent, color, msg, results;
				msg='';
				if (op=='progress') { 								// Transfer Progress
					if (e.lengthComputable) {
		            	percent=(e.loaded/e.total*100|0);
	            		color='blue';
		            	if (percent>=95) {
		            		percent=95;
		            		msg=' Please wait for Server verification...';
		            	}	 
		        	}
				} else if (op=='complete') {						// Transfer Complete
					try 	   { results=JSON.parse(e.currentTarget.response); }
					catch(err) { results=JSON.parse('{"rc":29}'); 			   } 
					if (results.rc==0) {
						percent=100;
						color='green';
					} else {
						percent=96;
						color='red';
						msg=' BAD Server response rc='+results.rc;
					}	
				}
				$ul['elProgressU'].setAttribute('aria-valuenow', percent);
				$ul['elProgressU'].style.width=percent+'%';
				$ul['elProgressU'].innerHTML=percent+'% Uploaded'+msg;
				$ul['elProgressU'].style.backgroundColor=color;	
			//	console.log(percent);
			}
		}

	</script>
@endsection
