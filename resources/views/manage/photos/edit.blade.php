@extends('manage')

@section('title','| Manage Edit Photo')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 		!!}
	{!! Html::style('css/select2.min.css')	!!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-image mr-4"></span>Edit Photo</a></h1>
			<hr>

			{!! Form::model($photo, ['route'=>['photos.update', $photo->id], 'method'=>'PUT', 'data-parsley-validate'=>'', 'files'=>true, 'autofocus'=>'']) !!}

			<div width="100%">
				{{ Form::label('title', 'Title:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'5', 'data-parsley-maxlength'=>'191', 'autofocus'=>'']) }}
			</div>

			{{ Form::label('album_ids', 'Albums:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('album_ids[]', $albums, $photo->albums, ['class'=>'form-control select2-multi', 'placeholder'=>'Select one or more Albums...', 'data-parsley-required'=>'', 'multiple'=>'']) }}

			{{ Form::label('tags', 'Tags:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('tags[]', $tags, null, ['class'=>'form-control select2-multi', 'multiple'=>'']) }}

				{{-- Select and preview an image file ---------------------------------------------------------------------- --}}
				{{-- Just change the data- values on the row div                                                             --}}
				{{ Form::label('', 'Image:', ['class'=>'font-bold form-spacing-top']) }}
				<div class="row ml-auto myFile-img" data-imgNew="myImgNew-1" data-imgOld="myImgOld-1" data-img="{{ $photo->image }}">
					<div class="col-md-9 custom-file" onChange="myFile(this)">
						{{ Form::file('image', ['class'=>'form-control custom-file-input', 'accept'=>'image/*']) }} 
						{{ Form::label('image', 'Select a file...', ['class'=>'custom-file-label']) }}
					</div>
					<div class="col-md-3 myFile-img-delete" style="display:none">
						{!! Html::decode(Form::label(
							'delete_image', '<i class="fas fa-trash-alt mr-2"></i>Delete Image',
							['class'=>'btn btn-outline-danger btn-block mb-0', 'onclick'=>"myImage(this, 'delete')"]
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

			{{ Form::label('description', 'Description:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::textarea('description', null, ['class'=>'form-control', 'id'=>'textarea-description', 'rows'=>'3']) }}
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">Albums:</dt>
					<dd class="col-sm-7">
						@foreach ($photo->albums as $album)
							<a href="{{ route('albums.show', $album->id) }}"><span class="badge badge-info">{{ $album->slug }}</span></a>
						@endforeach	
					</dd>
					<dt class="col-sm-5">Photo ID:</dt>
					<dd class="col-sm-7"><a href="{{ route('photos.show', $photo->id) }}">{{ $photo->id }}</a></dd>
					<dt class="col-sm-5">Snapped:</dt>						
					<dd class="col-sm-7">
						@if($photo->taken_at)
							{{ date('j M Y, h:i a', strtotime($photo->taken_at)) }}
						@endif
					</dd>
					<dt class="col-sm-5">Published:</dt>
					<dd class="col-sm-7">
						@if($photo->published_at)
							{{ date('j M Y, h:i a', strtotime($photo->published_at)) }}
						@else	
							<span class="text-danger">{{ $status_list[$photo->status] }}</span>
						@endif	
					</dd>
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($photo->created_at)) }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($photo->updated_at)) }}</dd>
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
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-edit mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('photos.index', '<i class="fas fa-images mr-2"></i>See All Photos', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImgOld-1" style="display:none">
					{{-- Used in Edit only 												--}}
					<img src="{{ asset('images/'.$photo->image) }}" width="100%" />
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
	{!! Html::script('js/parsley.min.js')	!!}
	{!! Html::script('js/select2.min.js')	!!}
	{!! Html::script('js/tinymce.min.js')	!!}

	<script type="text/javascript">
		$.fn.select2.defaults.set( "width", "100%" );
		// Above line must be first to ensure Select2 works
		// nicely alongside Bootrap 4   
		$('.select2-multi').select2({
			placeholder: "Select one or more..."
		});	</script>

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
