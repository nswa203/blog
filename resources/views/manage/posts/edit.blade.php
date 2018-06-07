@extends('manage')

@section('title','| Manage Edit Post')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 		!!}
	{!! Html::style('css/select2.min.css')	!!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a id="menu-toggle2"><span class="fas fa-file-alt mr-4"></span>Edit Post</a></h1>
			<hr>
			{!! Form::model($post,['route'=>['posts.update', $post->id], 'method'=>'PUT', 'data-parsley-validate'=>'', 'files'=>true]) !!}

			<div id="app" width="100%">
				{{ Form::label('title', 'Title:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'8', 'data-parsley-maxlength'=>'191', 'v-model'=>'title']) }}
				
				<slugwidget2 url="{{ url('/') }}" subdirectory="/" :title="title" @slug-changed="updateSlug"></slugwidget2>
			</div>

			{{ Form::label('category_id', 'Category:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('category_id', $categories,null, ['class'=>'form-control custom-select', 'placeholder'=>'Select a Category...', 'data-parsley-required'=>'']) }}

			{{ Form::label('tags', 'Tags:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('tags[]', $tags, null, ['class'=>'form-control select2-multi', 'multiple'=>'']) }}

			{{ Form::label('', 'Add Featured Image:', ['class'=>'font-bold form-spacing-top']) }}
			<div class="row">
				<div class="col-md-9">
					<div class="custom-file">
						{{ Form::file('featured_image', ['class'=>'form-control custom-file-input', 'id'=>'myFile-file', 'accept'=>'image/*']) }} 
						{{ Form::label('featured_image', 'Select a file...', ['class'=>'custom-file-label', 'id'=>'myFile-label']) }}
					</div>
				</div>

				<div class="col-md-3">
					<div id="myDelete" style="display:none;">
						<label class="form-control btn btn-outline-danger">
							<input type="checkbox" name="delete_image" value="1" hidden id="myDelete-check" onclick="myImage('delete')"><span class="fas fa-trash-alt mr-2"></span>Delete Image
						</label>
					</div>
					<div id="myReset" style="display:none;">
						<label class="form-control btn btn-outline-dark">
							<input type="checkbox" name="$" hidden onclick="myImage('reset')"><span class="fas fa-sync-alt mr-2"></span>Reset Image
						</label>
					</div>
				</div>
			</div>						
			
			{{ Form::label('author_id','Author:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::select('author_id', $users, null, ['class'=>'form-control custom-select', 'placeholder'=>'Select an Author...', 'data-parsley-required'=>'']) }}

			{{ Form::label('body', 'Body:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::textarea('body', null, ['class'=>'form-control', 'id'=>'textarea-body', 'data-parsley-required'=>'']) }}

			{{ Form::label('excerpt', 'Excerpt:', ['class'=>'font-bold form-spacing-top']) }}
			{{ Form::textarea('excerpt', null, ['class'=>'form-control', 'rows'=>'3', 'placeholder'=>'Leave empty to auto generate...']) }}
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url($post->slug) }}">{{ url($post->slug) }}</a></dd>
						<dt class="col-sm-5">Post ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('posts.show', $post->id) }}">{{ $post->id }}</a></dd>
						<dt class="col-sm-5">Category:</dt>						
						<dd class="col-sm-7">
							<a href="{{ route('categories.show', $post->category_id) }}"><span class="badge badge-info">{{ $post->category_name }}</span></a>
						</dd>
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
							@if($post->published_at)
								{{ date('j M Y, h:i a', strtotime($post->published_at)) }}
							@else	
								{{ $post->status_name }}
							@endif	
						</dd>							
						<dt class="col-sm-5">Author:</dt>
						<dd class="col-sm-7">{{ $post->author_name }}</dd>													
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->updated_at)) }}</dd>
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
						{!! Html::decode(link_to_route('posts.index', '<i class="fas fa-file-alt mr-2"></i>See All Posts', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImage-1" style="display:none">
					{{-- Used in Edit only 												--}}
					<img src="{{ asset('images/'.$post->image) }}" width="100%" />
				</div>
				<div id="myImage-2" style="display:none">
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
	{!! Html::script('js/app.js') 			!!}

	<script type="text/javascript">
		$.fn.select2.defaults.set( "width", "100%" );
		// Above line must be first to ensure Select2 works
		// nicely alongside Bootrap 4   
		$('.select2-multi').select2();
	</script>

	<script>
		tinymce.init ({
			selector: '#textarea-body',
			plugins: "link lists",
			menubar: false,
			toolbar: ""
 		});
	</script>

	<script>
		myImage();

		// ========================================================================== //
		// Manipulate file in browser
		$("#myFile-file").change(function() {
		    renderImage(this.files[0], 'myImage-2');
		});
		function renderImage(file, tagID) {
			var reader=new FileReader();
			reader.onload=function(event) {
    			the_url=event.target.result
    			$('#'+tagID).html("<img src='" + the_url + "' width=100% />")
  			}	
			reader.readAsDataURL(file);
		}

		// ========================================================================== //
		// Hide / Show elements
		function myHideShowElement(tagIDs={}, op='none') {
			for (var i=0; i<tagIDs.length; i++){
				var el=document.getElementById(tagIDs[i]);
				if (el) {
					if (op=='block') { el.style.display='block'; }
					else 			 { el.style.display='none'; }	
				}	
			}	
		}

		// ========================================================================== //
		// Flip-flop image controls and display
		function myImage(op='reset') {
			if (op=='delete') {
				myHideShowElement(['myDelete', 'myImage-1']);
				myHideShowElement(['myReset'], 'block');
				document.getElementById('myFile-label').innerHTML='<p class="text-danger"><span class="fas fa-trash-alt mr-2"></span>Image will be Deleted.</p>';
			} else {
				var show=['myfile'];
				// If you get an error here just pass an empty dummy new Post object from the PostController
				var myImage={!! json_encode($post->image) !!};
				if (myImage) { show.push('myDelete', 'myImage-1'); }
				myHideShowElement(['myReset', 'myImage-2']);
				myHideShowElement(show, 'block');
				var el=document.getElementById('myDelete-check')
				if (el) { el.checked=false; }
				document.getElementById('myFile-file').value="";
				document.getElementById('myFile-label').innerHTML="Select a file...";
			}
		}

		// ========================================================================== //
		// Put the selected filename into Form::file  
		$('.custom-file-input').on('change', function(maxSize='256') {
    		let fileName=$(this).val().split('\\').pop();
    		var maxSize=50;
    		if (fileName.length>maxSize+3) {
    			var part=parseInt(maxSize/2);
    			fileName=fileName.substr(0,part) + '...' + fileName.substr(fileName.length-part, part);
    		}
    		$(this).siblings('.custom-file-label').addClass("selected").html(fileName);
 			myHideShowElement(['myDelete', 'myImage-1']);
			myHideShowElement(['myReset', 'myImage-2'],'block');
		});
	</script>

	<script>
		var app=new Vue({
			el: '#app',
			data: {
				title: this.title.value,
				slug: this.title.slug,
				api_token: '{{ Auth::user()->api_token }}',
				post_id: '{{ $post->id }}'
			},
			methods: {
				updateSlug: function(val){
					this.slug=val
				},
			}
		});
	</script>	
@endsection
