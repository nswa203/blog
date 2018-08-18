@extends('manage')

@section('title','| Manage Add Files')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 		!!}
	{!! Html::style('css/select2.min.css')	!!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder-open mr-4">
				</span>Add Files{{ $folder_id?' to: '.$folders[$folder_id]:'' }}</a></h1>
			<hr>

			{!! Form::open(['route'=>'files.store', 'data-parsley-validate'=>'', 'files'=>true]) !!}

			<div width="100%">
				{{ Form::label('title', 'Title:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title', null, ['class'=>'form-control form-control-lg', 'data-parsley-maxlength'=>'191',  'placeholder'=>'Leave empty to auto generate...', $folder_id?'autofocus':'']) }}
			</div>

			{{ Form::label('folder_id', 'Folder:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('folder_id', $folders, $folder_id, ['class'=>'form-control custom-select', 'data-parsley-required'=>'',
				'placeholder'=>'Select a destination folder...', $folder_id?'':'autofocus']) }}

			{{ Form::label('tags', 'Tags:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('tags[]', $tags, null, ['class'=>'form-control select2-multi', 'multiple'=>'']) }}

				{{-- Select a file and preview if its a media file --------------------------------------------------------- --}}
				{{-- Just change the data- values on the row div                                                             --}}
				{{ Form::label('', 'Files:', ['class'=>'font-bold form-spacing-top']) }}
				<span class="author-time">Good to drag & drop</span>
				<div class="row ml-auto myFile-img" data-imgNew="myImgNew-1" data-imgOld="myImgOld-1" data-mime="*">
					<div class="col-md-9 custom-file" onChange="myFiles(this)">
						{{ Form::file('files[]', ['class'=>'form-control custom-file-input', 'accept'=>$mimes, 'data-parsley-required'=>'', 'multiple'=>'']) }} 
						{{ Form::label('files', 'Select files to upload...', ['class'=>'custom-file-label']) }}
					</div>
					<div class="col-md-3 myFile-img-delete" style="display:none">
						{!! Html::decode(Form::label(
							'delete_image', '<i class="fas fa-trash-alt mr-2"></i>Delete Files',
							['class'=>'btn btn-outline-dark btn-block mb-0', 'onclick'=>"myImage(this, 'delete')"]
						)) !!}
						{{ Form::checkbox('delete_image', '1', false, ['class'=>'myFile-img-delCheck', 'hidden']) }}
					</div>
					<div class="col-md-3 myFile-img-reset" style="display:none">
						{!! Html::decode(Form::label(
							'reset_image', '<i class="fas fa-sync-alt mr-2"></i>Reset Files', 
							['class'=>'btn btn-outline-dark btn-block mb-0', 'onclick'=>"myImage(this, 'reset')"]
						)) !!}
						{{ Form::checkbox('reset_image', '1', false, ['class'=>'myFile-img-resCheck', 'hidden']) }}
					</div>
					<div class="myFile-meta" style="display:none">
						{{ Form::select('meta[]', [], null, ['class'=>'form-control myFile-meta-list', 'multiple'=>'']) }}
					</div>					
				</div>	
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>

				<hr class="hr-spacing-top">
				<dl class="row dd-nowrap">
					@foreach ($list['f'] as $index => $status)
						<dt class="col-sm-5 mt-1">{{ $loop->index == 0 ? 'Status:' : '' }}</dt>
						<dt class="col-sm-7 mt-1 mb-2">
							<label for="status-{{ $index }}" class="">
								{{ Form::radio('status', $index, substr($status, 0, 1) == '*' ? true : null, ['class'=>'', 'hidden'=>'', 'id'=>'status-' . $index]) }}
								<span class="span"> {{ substr($status, 0, 1) == '*' ? substr($status, 1) : $status }}</span>
							</label>
						</dt>
					@endforeach
				</dl>

				<hr class="hr-spacing-top">
				<dl class="row dd-nowrap">
					@foreach ($list['o'] as $index => $option)
						<dt class="col-sm-5 mt-1">{{ $loop->index == 0 ? 'Overwrite:' : '' }}</dt>
						<dt class="col-sm-7 mt-1 mb-2">
							<label for="option-{{ $index }}" class="">
								{{ Form::radio('option', $index, substr($option, 0, 1) == '*' ? true : null, ['class'=>'', 'hidden'=>'', 'id'=>'option-' . $index]) }}
								<span class="span"> {{ substr($option, 0, 1) == '*' ? substr($option, 1) : $option }}</span>
							</label>
						</dt>
					@endforeach
				</dl>

				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-plus-circle mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('files.index', '<i class="fas fa-folder-open mr-2"></i>See All Files', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImgOld-1" style="display:none">
					{{-- Used in Edit only --}}
					{{-- <img src="{{ asset('images/'.$photo->image) }}" width="100%" /> --}}
				</div>
	
				<div class="Preview" id="myImgNew-1" style="display:none"> 
					<div class="carousel-message"></div>
					<div class="progress mb-2">
						<div id="myProgress-1" class="progress-bar" role="progressbar"
							 style="width: 100%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">Preview Media Files
						</div>
					</div>
					<div id="carouselControls" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner image-crop-height" style="--croph:35vw">
							{{-- Uploading images will be rendered here --}}
						</div>
						<a class="carousel-control-prev" href="#carouselControls" role="button" data-slide="prev">
						    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
						    <span class="sr-only">Previous</span>
						</a>
						<a class="carousel-control-next" href="#carouselControls" role="button" data-slide="next">
						    <span class="carousel-control-next-icon" aria-hidden="true"></span>
						    <span class="sr-only">Next</span>
						</a>
					</div>
				</div>
			</div>

		{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js')	  !!}
	{!! Html::script('js/select2.min.js')	  !!}
	{!! Html::script('js/exif.js')			  !!}
	{!! Html::script('js/jsmediatags.min.js') !!}

	<script type="text/javascript">
		$.fn.select2.defaults.set( "width", "100%" );
		// Above line must be first to ensure Select2 works
		// nicely alongside Bootrap 4   
		$('.select2-multi').select2({
			placeholder: "Select one or more..."
		});
	</script>

	<script>
		myImageAll();

		// ========================================================================== //
		// Called @ page load to simulate Reset for each image unit  
		function myImageAll() {
			var elFiles=document.getElementsByClassName('myFile-img-reset');
			for (i=0; i< elFiles.length; i++) {
  				myImage(elFiles[i].firstElementChild);
			}	
		}

		// ========================================================================== //
		// Toggle visibity of image controls and image locations
		function myImage($this, op='reset', imgNew=false, imgOld=false, img=false) {
			var elRow=$this.parentNode.parentNode;										// Owning DIV
			// console.dir(elRow); alert('!1');
			if (!imgNew){ imgNew=elRow.getAttribute('data-imgNew'); } 					// data-imgNew
			if (!imgOld){ imgOld=elRow.getAttribute('data-imgOld'); } 					// data-imgOld

			var elFile  	=elRow.getElementsByClassName('custom-file-input'	)[0];	// File Input
			var elLabel 	=elRow.getElementsByClassName('custom-file-label'	)[0];	// File Label	
			var elDelete	=elRow.getElementsByClassName('myFile-img-delete'	)[0];	// Delete Button
			var elDelCheck	=elRow.getElementsByClassName('myFile-img-delCheck'	)[0];	// Delete CheckBox
			var elReset 	=elRow.getElementsByClassName('myFile-img-reset' 	)[0];	// Reset Button
			var elMeta 		=elRow.getElementsByClassName('myFile-meta-list' 	)[0];	// Meta List

			if (op=='delete') {
				myHideShowElement([elDelete, imgOld]);
				myHideShowElement([elReset], 'block');
				elLabel.innerHTML='<p class="text-danger"><i class="fas fa-trash-alt mr-2"></i>Image will be Deleted.</p>';
			} else {
				if (img) {
					var show=[elFile, elDelete, imgOld];
				} else {
					var show=[elFile];
				}
				myHideShowElement([elReset, imgNew]);
				myHideShowElement(show, 'block');
				elDelCheck.checked=false;
				elFile.value='';
				elLabel.textContent="Select one or more files to upload...";
			}
		}

		// ========================================================================== //
		// Iterate a list of files.
		// Clear any existing preview slides. Load & render any image, video or audio
		// files and return the counts of each type of file file processed.
		function previewMedia(files, tagID, elMeta) {
			preview=document.querySelector('#'+tagID);
			messages=preview.childNodes[1];
			progress=preview.childNodes[3].childNodes[1];
			controls=preview.childNodes[5];
			slides=controls.childNodes[1];

			// ======================================================================= //
			// Render Images
			function doItImage(file, index) {
				var reader=new FileReader();
				reader.addEventListener("load", function () {
					var wrapper=document.createElement("div");
					if (index==0) {
						wrapper.className='carousel-item active';
					} else {
						wrapper.className='carousel-item';
					}

					var image=new Image();
					image.alt=file.name;
					image.src=this.result;
					image.className='d-block w-100';
					
					var link=document.createElement("A");
					link.href=this.result;

					var caption=document.createElement('div');
					caption.className='carousel-caption d-none d-md-block' 
					caption.innerHTML='<p><h5>'+file.displayName+'</h5></p>';

					link.appendChild(image);
					wrapper.appendChild(link);
					wrapper.appendChild(caption);
					slides.appendChild(wrapper);
				}, false);

				reader.readAsDataURL(file);
			}

			// ======================================================================= //
			// Render Video & Audio files
			function doItVideo(file, index, sound=false) {
				var reader=new FileReader();
				elPbar=progress;
				reader.onprogress=function(event) {
				    if (event.lengthComputable) {
 						percent=Math.round((event.loaded/event.total)*100);
						elPbar.setAttribute('aria-valuenow', percent);
						elPbar.style.width=percent+'%';
						elPbar.innerHTML=percent+'%';
				    }
				};

				reader.onloadend=function(event) {
					if (event.target.error!=null) { msg="Load failed!"; }
					else 				          {	msg='100%'; }		
					elPbar.setAttribute('aria-valuenow', 100);
					elPbar.style.width=100+'%';
					elPbar.innerHTML=msg;
				};

				reader.addEventListener('load', function () {
					var wrapper=document.createElement('div');
					if (index==0) {
						wrapper.className='carousel-item active';
					} else {
						wrapper.className='carousel-item';
					}

					// use "css video { width: 100%  !important; height: auto !important; }"
					var video = document.createElement('video');
					video.alt=file.name;
					video.src=this.result;
					video.controls    = true;
					video.autoplay    = !sound|(sound&&index==-1); // Change -1 to 0 for auto play
					video.muted       = !sound;
					video.loop        = !sound;
					video.playsinline = true;
					video.poster      = '{{ asset('favicon.ico') }}';

					// Retrieve embeded mp3 image data and set it as alternative
					if (sound) {
						jsmediatags.read(file, {
					        onSuccess: function(tag) {
					        	tags=tag.tags;
								image=tags.picture;
					          	if (image) {
						            base64String='';
						            for (i=0; i<image.data.length; i++) {
						                base64String+=String.fromCharCode(image.data[i]);
						            }
						            base64="data:image/jpeg;base64,"+window.btoa(base64String);
									video.poster=base64;
					          		//console.log(tags);
						        }    
					        }
					    });
					}

					// Add a caption 
					var caption=document.createElement('div');
					caption.className='carousel-caption d-none d-md-block' 
					caption.innerHTML='<p><h5>'+file.displayName+'</h5></p>';

					wrapper.appendChild(video);
					wrapper.appendChild(caption);
					slides.appendChild(wrapper);
				}, false);

				if (file.size>25000000) {
					reader.readAsDataURL(file.slice(0, 25000000))
				} else {
					reader.readAsDataURL(file);
				}	 	
			}

			var allowedImage = /(\.jpg|\.jpeg|\.png|\.gif|\.jpe|\.bmp|\.ico)$/i;
			var allowedVideo = /(\.mp4|\.mov|\.avi|\.mkv|\.mpg|\.mts|\.flv)$/i;
			var allowedAudio = /(\.mp3|\.wav|\.flac|\.wma)$/i;
			elMeta.innerText=null;
			slides.innerHTML='';

			images=0; videos=0; audios=0; others=0;
			for (i=0, len=files.length; i<len; i++) {

				var msg=files[i].name;
		    	var maxSize=20;
	    		if (msg.length>maxSize+3) {										// Trim for Label
	    			var part=parseInt(maxSize/2);
	    			msg=msg.substr(0, part) + '...' + msg.substr(msg.length-part, part);
	    		}
	    		files[i].displayName=msg;	    		

				if (allowedImage.exec(files[i].name)) {
					doItImage(files[i], i);
					myGetExif(files[i], elMeta);
					images++;
				} else if (allowedVideo.exec(files[i].name)) {
					doItVideo(files[i], i);
					videos++;
				} else if (allowedAudio.exec(files[i].name)) {
					doItVideo(files[i], i, true);
					audios++;
				} else { others++; }  	
			}
			return [images, videos, audios, others];	
		}

		// ========================================================================== //
		// If a single file, put the filename of the selected file into Form::file->label
		// Otherwise put the files count into Form::file->label
		// If a media file, load and render the NEW file
		// Hide the DELETE button and any OLD images
		// Show the RESET  button and any NEW images 
		function myFiles($this, imgNew=false, imgOld=false, mimes=false) {
			var elRow=$this.parentNode;										    // Owning DIV
			if (!imgNew){ imgNew=elRow.getAttribute('data-imgNew'); } 			// data-imgNew
			if (!imgOld){ imgOld=elRow.getAttribute('data-imgOld'); } 			// data-imgOld
			if (!mimes) { mimes =elRow.getAttribute('data-mime'); } 			// data-mime

			var elInput =$this.childNodes[1]; 									// file input element
			var elLabel =$this.childNodes[3]; 									// file label element
			var elDelete=elRow.getElementsByClassName('myFile-img-delete')[0];	// Delete Button
			var elReset =elRow.getElementsByClassName('myFile-img-reset')[0];	// Reset Button
			var elMeta	=elRow.getElementsByClassName('myFile-meta-list')[0];	// Meta List

    		myHideShowElement([elDelete, imgOld]);								// Hide
	    	rendered=previewMedia(elInput.files, imgNew, elMeta);				// Load & render images/videos/audios
	    	if (rendered[0]+rendered[1]+rendered[2]>0) {         											
				myHideShowElement([elReset, imgNew], 'block');					// Show images/videos/audios & Reset
			} else {
	    		myHideShowElement([imgNew]);									// Hide
				myHideShowElement([elReset], 'block');							// Show Reset
			}	

			if (elInput.files.length==1) {										// Inject fileName / count	
				var msg=elInput.files[0].name;
		    	var maxSize=50;
	    		if (msg.length>maxSize+3) {										// Trim for Label
	    			var part=parseInt(maxSize/2);
	    			msg=msg.substr(0, part) + '...' + msg.substr(msg.length-part, part);
	    		}	    		
	    	} else { msg=elInput.files.length+' files selected: '+rendered; }
    		elLabel.textContent=msg;
    	}

		// ========================================================================== //
		// Retrieve EXIF & IPTC metadata from image file and append to output element  
		function myGetExif(file, elOut) {
			EXIF.getData(file, function() {
		        var newMetaData=EXIF.getAllTags(this);
		        newMetaData.filename=file.name;
				var newItem=document.createElement("option");
		       	newItem.text=JSON.stringify(newMetaData, null, "\t");
		       	elOut.add(newItem);
		    });
		}

		// ========================================================================== //
		// Hide / Show elements - input is a list of ids OR element objects
		function myHideShowElement(tagIDs={}, op='none') {
			for (var i=0; i<tagIDs.length; i++){
				var el=tagIDs[i];
				if (typeof el === 'string') {
					el=document.getElementById(el);
				}	
				if (el) {
					if (op=='block') { el.style.display='block';}
					else 			 { el.style.display='none'; }	
				}	
			}	
		}
	</script>	
@endsection
