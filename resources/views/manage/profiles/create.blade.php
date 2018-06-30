@extends('manage')

@section('title','| Manage Create Profile')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a id="menu-toggle2"><span class="fas fa-user-circle mr-4"></span>Create User Profile</a></h1>
			<hr>
			<div class="image-crop-height mt-3 mb-0" id="myImgOld-2" style="--croph:232px; display:none">
				<img src="{{ asset('images/'.$profile->banner) }}" width="100%" />
			</div>
			<div class="image-crop-height mt-3 mb-0" id="myImgNew-2" style="--croph:232px; display:none">
				<img src="{{ asset('images/'.$profile->banner) }}" width="100%" />
			</div>

			{!! Form::open(['route'=>'profiles.store', 'data-parsley-validate'=>'', 'files'=>true]) !!}
			
				{{ Form::label('user_id','Registered User:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('user_id', $users, auth()->user()->id, ['class'=>'form-control custom-select', 'placeholder'=>'Select a User that does not yet have a profile...', 'data-parsley-required'=>'']) }}

				{{ 	Form::label('username', 'Username:', ['class'=>'font-bold form-spacing-top']) }}
				{{ 	Form::text('username', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'autofocus'=>'']) }}

				{{-- Select and preview an image file ---------------------------------------------------------------------- --}}
				{{-- Just change the data- values on the row div                                                             --}}
				{{ Form::label('', 'Image:', ['class'=>'font-bold form-spacing-top']) }}
				<div class="row ml-auto myFile-img" data-imgNew="myImgNew-1" data-imgOld="myImgOld-1" data-img="{{ $profile->image }}">
					<div class="col-md-9 custom-file" onChange="myFile(this)">
						{{ Form::file('image', ['class'=>'form-control custom-file-input', 'accept'=>'image/*' ]) }} 
						{{ Form::label('image', 'Select a file...', ['class'=>'custom-file-label']) }}
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
						{{ Form::checkbox('reset_image','1', false, ['class'=>'myFile-img-resCheck', 'hidden']) }}
					</div>
				</div>	

				{{-- Select and preview an image file ---------------------------------------------------------------------- --}}
				{{-- Just change the data- values on the row div                                                             --}}
				{{ Form::label('', 'Banner:', ['class'=>'font-bold form-spacing-top']) }}
				<div class="row ml-auto myFile-img" data-imgNew="myImgNew-2" data-imgOld="myImgOld-2" data-img="{{ $profile->banner }}">
					<div class="col-md-9 custom-file" onChange="myFile(this)">
						{{ Form::file('banner', ['class'=>'form-control custom-file-input', 'accept'=>'image/*' ]) }} 
						{{ Form::label('banner', 'Select a file...', ['class'=>'custom-file-label']) }}
					</div>
					<div class="col-md-3 myFile-img-delete" style="display:none">
						{!! Html::decode(Form::label(
							'delete_banner', '<i class="fas fa-trash-alt mr-2"></i>Delete Image',
							['class'=>'btn btn-outline-dark btn-block mb-0', 'onclick'=>"myImage(this, 'delete')"]
						)) !!}
						{{ Form::checkbox('delete_banner', '1', false, ['class'=>'myFile-img-delCheck', 'hidden']) }}
					</div>
					<div class="col-md-3 myFile-img-reset" style="display:none">
						{!! Html::decode(Form::label(
							'reset_banner', '<i class="fas fa-sync-alt mr-2"></i>Reset Image', 
							['class'=>'btn btn-outline-dark btn-block mb-0', 'onclick'=>"myImage(this, 'reset')"]
						)) !!}
						{{ Form::checkbox('reset_banner','1', false, ['class'=>'myFile-img-resCheck', 'hidden']) }}
					</div>
				</div>	

				{{ 	Form::label('phone', 'Phone:', ['class'=>'font-bold form-spacing-top mr-3']) }}
				{{ 	Form::text('phone', null, ['class'=>'form-control', 'data-parsley-maxlength'=>'191']) }} 

				{{ 	Form::label('address', 'Address:', ['class'=>'font-bold form-spacing-top mr-3']) }}
				{{ 	Form::textarea('address', null, ['class'=>'form-control', 'rows'=>'3', 'data-parsley-maxlength'=>'191', 'id'=>'textarea-address']) }} 

				{{ 	Form::label('about_me', 'About Me:', ['class'=>'font-bold form-spacing-top mr-3']) }}
				{{ 	Form::textarea('about_me', null, ['class'=>'form-control', 'rows'=>'3', 'id'=>'textarea-about_me']) }} 
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>

				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-plus-circle mr-2"></i>Create', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('profiles.index', '<i class="fas fa-user-circle mr-2"></i>See All Profiles', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImgOld-1" style="display:none">
					{{-- Used in Edit only 												--}}
					<img src="{{ asset('images/'.$profile->image) }}" width="100%" />
				</div>
				<div id="myImgNew-1" style="display:none">
					{{-- Uploading image will be rendered here --}}
				</div>
			</div>

		{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/tinymce.min.js') !!}

	<script>
		tinymce.init ({
			selector: '#textarea-address',
			plugins: "link lists",
			menubar: false,
			toolbar: "",
			forced_root_block : 'div',
 		});

 		tinymce.init ({
			selector: '#textarea-about_me',
			plugins: "link lists",
			menubar: false,
			toolbar: "",
			forced_root_block : 'div',
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
			if (!img   ){ img   =elRow.getAttribute('data-img'   ); } 					// data-img

			var elFile  	=elRow.getElementsByClassName('custom-file-input'	)[0];	// File Input
			var elLabel 	=elRow.getElementsByClassName('custom-file-label'	)[0];	// File Label	
			var elDelete	=elRow.getElementsByClassName('myFile-img-delete'	)[0];	// Delete Button
			var elDelCheck	=elRow.getElementsByClassName('myFile-img-delCheck'	)[0];	// Delete CheckBox
			var elReset 	=elRow.getElementsByClassName('myFile-img-reset' 	)[0];	// Reset Button

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
				elLabel.textContent="Select a file...";
			}
		}

		// ========================================================================== //
		// Loads a file and renders the image to the specied tag
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
			var elRow   =$this.parentNode;										// Owning DIV
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
