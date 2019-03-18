@extends('manage')

@section('title','| Manage Edit Folder')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 		!!}
	{!! Html::style('css/select2.min.css')	!!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder-open mr-4"></span>Edit Folder</a></h1>
			<hr>

			{!! Form::model($folder,['route'=>['folders.update', $folder->id], 'method'=>'PUT', 'data-parsley-validate'=>'', 'files'=>true]) !!}

			<div id="app" width="100%">
				{{ Form::label('title', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'v-model'=>'title']) }}
				
				<slugwidget url="{{ url('/fo') }}" subdirectory="/" :title="title" @slug-changed="updateSlug"></slugwidget>
			</div>

			{{ Form::label('category_id', 'Category:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('category_id', $categories,null, ['class'=>'form-control custom-select', 'placeholder'=>'Select a Category...', 'data-parsley-required'=>'']) }}

				{{-- Select and preview an image file ---------------------------------------------------------------------- --}}
				{{-- Just change the data- values on the row div                                                             --}}
				{{ Form::label('', 'Image:', ['class'=>'font-bold form-spacing-top']) }}
				<span class="author-time">Good to drag & drop</span>
				<div class="row ml-auto myFile-img" data-imgNew="myImgNew-1" data-imgOld="myImgOld-1" data-img="{{ $folder->image }}">
					<div class="col-md-9 custom-file" onChange="myFile(this)">
						{{ Form::file('image', ['class'=>'form-control custom-file-input', 'accept'=>'image/*' ]) }} 
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

			{{ Form::label('max_size', 'Maximum Size: MB', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::text('max_size', null, ['class'=>'form-control', 'placeholder'=>'Set the Maximum data space size (MB) for this Folder...', 'data-parsley-required'=>'']) }}

			{{ Form::label('user_id','Username:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('user_id', $users, null, ['class'=>'form-control custom-select', 'placeholder'=>'Select an Author...', 'data-parsley-required'=>'']) }}

			{{ Form::label('description', 'Description:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::textarea('description', null, ['class'=>'form-control', 'id'=>'textarea-description', 'rows'=>'3']) }}
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
	
				@include('partials.__foldersMeta')

				@foreach ($list['d'] as $index => $status)
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
						{!! Html::decode('<a href="" class="btn btn-danger btn-block" onclick="
							window.history.back();
							event.preventDefault ? event.preventDefault : event.returnValue=false;">
						<span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-edit mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('folders.index', '<i class="fas fa-folder-open mr-2"></i>See All Folders', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImgOld-1" style="display:none">
					{{-- Used in Edit only 												--}}
					<img src="{{ route('folders.getFolderFile', [$folder->id, 'Folder.jpg']) }}" width="100%" />
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
	{!! Html::script('js/select2.min.js') !!}
	{!! Html::script('js/tinymce.min.js') !!}
	{!! Html::script('js/app.js') 		  !!}
	{!! Html::script('js/helpers.js') 	  !!}

	<script type="text/javascript">
		$.fn.select2.defaults.set( "width", "100%" );
		// Above line must be first to ensure Select2 works
		// nicely alongside Bootrap 4   
		$('.select2-multi').select2({
			placeholder: "Select one or more..."
		});	</script>

	<script>
		// ========================================================================== //
		// Initialises tinymce with standard settings   
		// place this at the end of your view myTinymce('#textarea-description');
		myTinymce('textarea-description');
	</script>

	<script>
		myImageAll(myImageInit());
		// ========================================================================== //
		// Sets up constants required by myImagAll() myImage() myFile()  
		// Should be called first to make myImageVars available Globally
		// Best to place your own custom copy at the end of your view 
		function myImageInit() {
			myImageVars = {
				attr_image_new:        'data-imgNew', 		  // data-imgNew
				attr_image_old:        'data-imgOld', 		  // data-imgOld
				attr_image:            'data-img', 			  // data-img
				class_input_file:      'custom-file-input',   // File Input
				class_label_file:      'custom-file-label',   // File Label
				class_button_delete:   'myFile-img-delete',   // Delete Button
				class_button_delCheck: 'myFile-img-delCheck', // Delete CheckBox
				class_button_reset:    'myFile-img-reset'  	  // Reset Button
			};	
		};
	</script>	

	<script>
		var app=new Vue({
			el: '#app',
			data: {
				title: this.title.value,
				slug: this.title.slug,
				api_token: '{{ Auth::user()->api_token }}',
				resource_id: '{{ $folder->id }}',
				route: '{{ route("api.folders.unique") }}'
			},
			methods: {
				updateSlug: function(val){
					this.slug=val
				}
			}
		});
	</script>
@endsection
