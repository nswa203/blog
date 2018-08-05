@extends('manage')

@section('title','| Manage Add Multiple Photos')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 		!!}
	{!! Html::style('css/select2.min.css')	!!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-image mr-4"></span>Add Multiple Photos</a></h1>
			<hr>

			{!! Form::open(['route'=>'photos.storeMultiple', 'id'=>'myForm-1', 'data-parsley-validate'=>'', 'files'=>true]) !!}

			<div width="100%">
				{{ Form::label('title', 'Title:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title', $album->title, ['class'=>'form-control form-control-lg', 'data-parsley-maxlength'=>'191', 'autofocus'=>'']) }}
			</div>

			{{ Form::label('album_ids', 'Albums:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('album_ids[]', $albums, $album->id, ['class'=>'form-control select2-multi custom-select', 'placeholder'=>'Select one or more Albums...', 'data-parsley-required'=>'', 'multiple'=>'']) }}

			{{ Form::label('tags', 'Tags:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('tags[]', $tags, $album_tags, ['class'=>'form-control select2-multi', 'multiple'=>'']) }}

				{{-- Select and preview multiple image files --------------------------------------------------------------- --}}
				{{-- Just change the data- values on the row div                                                             --}}
				{{ Form::label('', 'Images New:', ['class'=>'font-bold form-spacing-top']) }}
				<div class="row ml-auto myFile-img" data-imgNew="myImgNew-1" data-imgOld="myImgOld-1" data-img="{{ $photo->image }}">
					<div class="col-md-9 custom-file" onChange="myFiles(this)">
						{{ Form::file('image[]', ['class'=>'form-control custom-file-input', 'accept'=>'image/*', 'multiple'=>'', 'data-parsley-required'=>'1', 'data-parsley-max-files'=>'64']) }} 
						{{ Form::label('image[]', 'Select one or more files...', ['class'=>'custom-file-label']) }}
					</div>
					<div class="col-md-3 myFile-img-delete" style="display:none">
						{!! Html::decode(Form::label(
							'delete_image', '<i class="fas fa-trash-alt mr-2"></i>Delete Image',
							['class'=>'btn btn-outline-dark btn-block mb-0', 'onclick'=>"myImage(this, 'delete')"]
						)) !!}
						{{ Form::checkbox('delete_image', '1', false, ['class'=>'myFile-img-delCheck', 'hidden']) }}
					</div>
					<div class="col-md-3 myFile-img-reset" style="display:none">
						{!! Html::decode(Form::label(
							'reset_image', '<i class="fas fa-sync-alt mr-2"></i>Reset Image', 
							['class'=>'btn btn-outline-dark btn-block mb-0', 'onclick'=>"myImage(this, 'reset')"]
						)) !!}
						{{ Form::checkbox('reset_image', '1', false, ['class'=>'myFile-img-resCheck', 'hidden']) }}
					</div>
					<div class="myFile-meta" style="display:none">
						{{ Form::select('meta[]', [], null, ['class'=>'form-control myFile-meta-list', 'multiple'=>'']) }}
					</div>
				</div>	
				<div class="progress" style="background-color: white;">
				  <div id="myPbar-1" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
				</div>
<div id="message"></div>

			{{ Form::label('description', 'Description:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::textarea('description', $album->description, ['class'=>'form-control', 'id'=>'textarea-description', 'rows'=>'3']) }}
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>

				<hr class="hr-spacing-top">
				@foreach ($status_list as $index => $status)
					<dt>
						<div class="field">
							<label for="status-{{ $index }}" class="mb-3">
								{{ Form::radio('status', $index, substr($status, 0, 1) == '*' ? true : null, ['class'=>'', 'hidden'=>'', 'id'=>'status-' . $index]) }}
								<span class="span"> {{ substr($status, 0, 1) == '*' ? substr($status, 1) : $status }}</span>
							</label>
						</div>
					</dt>
				@endforeach

				<hr class="hr-spacing-top mt-1">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block cancel"><span class="fas fa-times-circle mr-2" name="cancel"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-plus-circle mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block', 'name'=>'save']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('photos.index', '<i class="fas fa-image mr-2"></i>See All Photos', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImgOld-1" style="display:none">
					{{-- Used in Edit only --}}
					<img src="{{ asset('images/'.$photo->image) }}" width="100%" />
				</div>
				<div id="myImgNew-1" style="display:none">
					<div id="carouselControls" class="carousel slide" data-ride="carousel">
						<div class="carousel-inner image-crop-height" style="--croph:500px">
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
	{!! Html::script('js/parsley.min.js')	!!}
	{!! Html::script('js/select2.min.js')	!!}
	{!! Html::script('js/tinymce.min.js')	!!}
	{!! Html::script('js/exif.js')			!!}

	<script>
		window.Parsley.addValidator('maxFiles', {
		    requirementType: 'integer',
		    validateString (value, requirement, parsleyInstance) {
		    	var files=parsleyInstance.$element[0].files; 
		      	return files.length<=requirement;
		    },
		    messages: {
			    en: 'Too many files - select a maximum of %s.'
		    }
		})
	</script>

	<script type="text/javascript">
		$.fn.select2.defaults.set( "width", "100%" );
		// Above line must be first to ensure Select2 works
		// nicely alongside Bootrap 4   
		$('.select2-multi').select2({placeholder: 'Select one or more options...', closeOnSelect: false});
	</script>

	<script>
		tinymce.init ({
			selector: '#textarea-description',
			plugins: "link lists",
			menubar: false,
			toolbar: "",
			forced_root_block : 'div',
            branding: false,
 		});
	</script>

	<script>
		myImageAll();

		// ========================================================================== //
		// Load all files using Ajax to manage a progress bar
		// formID, formSubmitButtonName, formInputFileName, ProgressBarID  
		myUploadFiles2({
			formID: 				'myForm-1', 						// Used
			formSubmitButtonName: 	'save',								// Used				
			formCancelButtonName: 	'cancel',			
			formInputFileName: 		'image[]',
			formMsgs: 				'message',
			progressBarID: 			'myPbar-1',
			url: 					'/manage/photos/storeMultiple'
		});

		function myUploadFiles2(p) {
			var elForm=document.getElementById(p.formID);				// Used
			var elSave=elForm.elements[p.formSubmitButtonName];			// Used
			var elCancel=document.getElementsByClassName('cancel')[0];	// Used
			elSave.addEventListener('click', function(e) {
				e.preventDefault();
				myUploadFilesSave(p);
				});
			elCancel.addEventListener('click', function(e) {
				e.preventDefault();
				myUploadFilesCancel(p);
				});		
			}
		function sleep(ms) {
		  return new Promise(resolve=>setTimeout(resolve, ms));
		}			
		async function myUploadFilesSave(p) {
			var elForm=document.getElementById(p.formID);				// Used
			var elFile=elForm.elements[p.formInputFileName];
			var elMsgs=document.getElementById(p.formMsgs);
			var elPbar=document.getElementById(p.progressBarID);

			var fd=new FormData(elForm);
			var url=p.url + ((/\?/).test(p.url) ? "&" : "?") + (new Date()).getTime(); // Force NO cache
			p.totalFiles=elFile.files.length;
			p.files=0;
			p.sizes=0;
			p.totalSizes=0;
			for (var i=0; i<p.totalFiles; i++){
				p.totalSizes=p.totalSizes+elFile.files[i].size;
			}

			p.lock=false;
			for (var i=0; i<p.totalFiles; i++){
				while (p.lock) { 
					await sleep(250);
				}

				file=elFile.files[i];
				fd.set('image[]', file, file.name);
	
				var xhr=new XMLHttpRequest();
				p.xhr=xhr;
			
				xhr.onerror=function() {
					console.log('XHR Fatal Error!');
				};	
				xhr.upload.onloadstart=function() {
					console.log('XHR Upload Start');
				};	
				xhr.upload.onprogress=function(e) {
					console.log('XHR Upload Progress: '+e.loaded+' of '+e.total );
                    //elMsgs.innerHTML='XHR Upload Progress: '+e.loaded+' of '+e.total;
                    var bytes=p.sizes+e.loaded;
                    var percent=Math.round((bytes/p.totalSizes)*100);
                    percent=percent<100?percent:99;
                    elMsgs.innerHTML='Files: '+p.files+'.5 of '+p.totalFiles+' -  Bytes: '+bytes+' of '+p.totalSizes+' '+percent+'%';
					elPbar.setAttribute('aria-valuenow', percent);
					elPbar.style.width=percent+'%';
					elPbar.innerHTML=percent+'%';
				};
				xhr.upload.onloadend=function(e) {
					console.log('XHR Upload End - Waiting for Server confirmation...');
                    //elMsgs.innerHTML='XHR Upload End - Waiting for Server confirmation...';
				};
				xhr.onreadystatechange=function(e) {
					console.log('XHR State Change: "'+xhr.readyState+'"');
					if (xhr.readyState==4) {
						if (p.totalFiles>0) {
							p.lock=false;
							p.files=p.files+1;
							p.sizes=p.sizes+file.size;
		                    var bytes=p.sizes;
		                    var percent=Math.round((bytes/p.totalSizes)*100);
		                    //percent=percent>2?percent-1:percent;
		                    elMsgs.innerHTML='Files: '+p.files+'.0 of '+p.totalFiles+' -  Bytes: '+bytes+' of '+p.totalSizes+' '+percent+'%';
							elPbar.setAttribute('aria-valuenow', percent);
							elPbar.style.width=percent+'%';
							elPbar.innerHTML=percent+'%';
						} else {
		                    elMsgs.innerHTML='File upload CANCELLED.';
   							elPbar.style.backgroundColor='red';
						}	
					}
				};
				xhr.onload=function() {
					console.log('XHR Loaded 1 ENTRY:');
					//elMsgs.innerHTML=xhr.responseText;
					if (xhr.readyState === xhr.DONE) {
						console.log('XHR Loaded 2 DONE:');
						p.lock=false;
				        if (xhr.status === 200) {
   							console.log('XHR Loaded 3 STATUS 200:');
				        }
				    }
				};
				
				xhr.open('POST', url, true);

				p.lock=true;
				xhr.send(fd);
			}
		}	
		
		function myUploadFilesCancel(p) {
			console.log('myUFCancel '+p.formCancelButtonName);
			p.totalFiles=0;
			p.xhr.abort();
		}			

		function myUploadFiles(p){
			document.getElementById(p.formID).elements[p.formSubmitButtonName].addEventListener('click', function(e) {
				e.preventDefault();
				var elPbar=document.getElementById(p.progressBarID);
				var elForm=document.getElementById(p.formID);
				var elFile=elForm.elements[p.formInputFileName];
				var formData=new FormData(elForm);
				//formData.append('image[]', elFile.files);
				$.ajax({
					xhr: function() {
						var xhr=new window.XMLHttpRequest();
						xhr.upload.addEventListener('progress', function(e) {
							if (e.lengthComputable) {
								var percent=Math.round((e.loaded/e.total)*100);
								elPbar.setAttribute('aria-valuenow', percent);
								elPbar.style.width=percent+'%';
								elPbar.innerHTML=percent+'%';
								console.log('Bytes Loaded: '+e.loaded);
								console.log('Total Size: '+e.total);
								console.log('Percentage Uploaded: '+(e.loaded/e.total));
								alert(elFile.files.length+' '+e.total)
							}
						});	
						return xhr;
					},
					type: 		'POST',
					url: 		 p.url,
					data: 		 formData,
					cache: 		 false,
					processData: false,
					contentType: false,
					success: function (data) {
						alert('File Uploaded!');
				        console.log(data);
					},	
					error: function(data) {
						var errors=data.responseJSON;
				        console.log(errors);
					}
				});				
			});
		};
	
		function myUploadFile(file, i, p){
			alert('Trace: '+i+' '+file.name+' '+p.formID);

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
			if (!img   ){ img   =elRow.getAttribute('data-img'   ); } 					// data-img

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
				elLabel.textContent="Select one or more files...";
				elMeta.innerText=null;
			}
		}

		// ========================================================================== //
		// Loads a file and renders the image to the specified tag
		function renderImage(file, tagID) {
			//alert('Loading!');
			var reader=new FileReader();
			reader.onload=function(event) {
    			the_url=event.target.result
    			$('#'+tagID).html("<img src='" + the_url + "' width=100% />")
  			}	
			reader.readAsDataURL(file);
		}

		// ========================================================================== //
		// Put the filename of the selected file into Form::file->label  
		// Load and render the NEW image file
		// Hide the DELETE button and any OLD image
		// Show the RESET button and our NEW image 
		function myFile($this, imgNew=false, imgOld=false) {
			var elRow=$this.parentNode;											// Owning DIV
			if (!imgNew){ imgNew=elRow.getAttribute('data-imgNew'); } 			// data-imgNew
			if (!imgOld){ imgOld=elRow.getAttribute('data-imgOld'); } 			// data-imgOld			

			var elInput =$this.childNodes[1]; 									// file input element
			var elLabel =$this.childNodes[3]; 									// file label element
			var elDelete=elRow.getElementsByClassName('myFile-img-delete')[0];	// Delete Button
			var elReset =elRow.getElementsByClassName('myFile-img-reset')[0];	// Reset Button

			var fileName=elInput.files[0].name;
			var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.jpe)$/i;
			if (allowedExtensions.exec(fileName)) {
		    	var maxSize=50;
	    		if (fileName.length>maxSize+3) {								// Trim for Label
	    			var part=parseInt(maxSize/2);
	    			fileName=fileName.substr(0,part) + '...' + fileName.substr(fileName.length-part, part);
	    		}
	    		elLabel.textContent=fileName;

	    		renderImage(elInput.files[0], imgNew);							// Load image
	    		myHideShowElement([elDelete, imgOld]);							// Hide
				myHideShowElement([elReset,  imgNew], 'block');					// Show
			}	
    	}

		// ========================================================================== //
		// Loads multiple files and renders the images to the specified tag
		function renderImages(files, tagID, elMeta) {
			var preview=document.querySelector('#'+tagID).childNodes[1].childNodes[1];
			preview.innerHTML='';

			function doIt(file, index) {
				var reader=new FileReader();
				reader.addEventListener("load", function () {
					var fileName=file.name
					fileName=fileName.length <= 24 ? fileName : fileName.substr(0, 24);

					var wrapper=document.createElement("div");
					if (index==0) {
						wrapper.className='carousel-item active';
					} else {
						wrapper.className='carousel-item';
					}

					//var caption=document.createElement("div");
					//caption.className='carousel-caption d-none d-md-block text-dark';
					//caption.innerHTML='<h5> ' + fileName + '</h5>';

					preview.appendChild(wrapper);
					var image=new Image();
					image.alt=file.name;
					image.src=this.result;
					image.className='d-block w-100';
					wrapper.appendChild(image);
					//wrapper.appendChild(caption);
					//myGetExif(file, elMeta);
				}, false);
				reader.readAsDataURL(file);
			}

			elMeta.innerText=null;
			for (i=0, len=files.length; i<len; i++) {
				doIt(files[i], i, len);
	    		myGetExif(files[i], elMeta);
			}

		}

		// ========================================================================== //
		// Put the filename of the first selected file... into Form::file->label  
		// Load and render the NEW image files
		// Hide the DELETE button and any OLD images
		// Show the RESET button and our NEW images 
		function myFiles($this, imgNew=false, imgOld=false) {
			var elRow   =$this.parentNode;										// Owning DIV
			if (!imgNew){ imgNew=elRow.getAttribute('data-imgNew'); } 			// data-imgNew
			if (!imgOld){ imgOld=elRow.getAttribute('data-imgOld'); } 			// data-imgOld			

			var elInput =$this.childNodes[1]; 									// file input element
//			var elLabel =$this.childNodes[3]; 									// file label element
			var elLabel=elRow.getElementsByClassName('custom-file-label')[0];	// file label element

			var elDelete=elRow.getElementsByClassName('myFile-img-delete')[0];	// Delete Button
			var elReset =elRow.getElementsByClassName('myFile-img-reset' )[0];	// Reset Button
			var elMeta	=elRow.getElementsByClassName('myFile-meta-list')[0];	// Meta List

			var fileName=elInput.files[0].name;
			var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.jpe)$/i;
			if (allowedExtensions.exec(fileName)) {
		    	var maxSize=50;
	    		if (fileName.length>maxSize+3) {								// Trim for Label
	    			var part=parseInt(maxSize/2);
	    			fileName=fileName.substr(0, part) + '...' + fileName.substr(fileName.length-part, part);
	    		}
	    		if (elInput.files.length > 1) {fileName=fileName + '...(' + elInput.files.length + ')'}
	    		elLabel.textContent=fileName;

	    		renderImages(elInput.files, imgNew, elMeta);					// Load images
	    		myHideShowElement([elDelete, imgOld]);							// Hide
				myHideShowElement([elReset,  imgNew], 'block');					// Show
			}	
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
