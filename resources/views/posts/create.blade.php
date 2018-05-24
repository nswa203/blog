@extends('main')

@section('title','| Create New Post')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
	{!! Html::style('css/select2.min.css') !!}
	{!! Html::script('js/tinymce.min.js') !!}
	<script>
		tinymce.init ({
			selector: 'textarea',
			plugins: "link lists",
			menubar: false,
			toolbar: ""
 		});
	</script>
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8" >
			<h1><span class="fas fa-file-alt mr-4"></span>Create New Post</h1>
			<hr>
			{!! Form::open(['route'=>'posts.store','data-parsley-validate'=>'','files'=>true]) !!}
				{{ Form::label('title','Title:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title',null,['class'=>'form-control form-control-lg','data-parsley-required'=>'','data-parsley-maxlength'=>'191']) }}

				{{ Form::label('slug','Slug:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('slug',null,['class'=>'form-control','data-parsley-required'=>'','data-parsley-maxlength'=>'191','data-parsley-minlength'=>'5','placeholder'=>'your-slug']) }}

				{{ Form::label('category_id','Category:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('category_id',$categories,null,['class'=>'form-control custom-select','placeholder'=>'Select a Category...','data-parsley-required'=>'']) }}

				{{ Form::label('tags','Tags:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('tags[]',$tags,null,['class'=>'form-control select2-multi','multiple'=>'']) }}

				{{ Form::label('featured_image','Update Featured Image:',['class'=>'font-bold form-spacing-top']) }}
				<div class="custom-file float-left" style="width:80%">
					{{ Form::file('featured_image',						['class'=>'custom-file-input','id'=>'myFile-file']) }} 
					{{ Form::label('featured_image','Select a file...',	['class'=>'custom-file-label','id'=>'myFile-label']) }}
				</div>

				<div id="myDelete" style="display:none;">
					<label class="btn btn-outline-danger float-right" style="width:18%;">
						<input type="checkbox" name="delete_image" value="1" hidden id="myDelete-check" onclick="myImage('delete')">Delete Image
					</label>
				</div>
				<div id="myReset" style="display:none;">
					<label class="btn btn-outline-dark float-right" style="width:18%;">
						<input type="checkbox" name="$" hidden onclick="myImage('reset')">Reset Image
					</label>
				</div>
				<div style="clear:both;"></div>

				{{ Form::label('body','Body:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::textarea('body',null,['class'=>'form-control']) }}
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row">
					<dt class="col-sm-5">URL:</dt>
					<dd class="col-sm-7"><a href="#">{{ route('blog.single','your-slug') }}</a></dd>
					<dt class="col-sm-5">Category:</dt>
					<dd class="col-sm-7"><a href="#"><span class="badge badge-default">Select a Category...</span></a></dd>					
					<dt class="col-sm-5">Created At:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>
				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::LinkRoute('posts.index','Cancel',[],['class'=>'btn btn-danger btn-block']) !!}
					</div>
					<div class="col-sm-6">
						{{ Form::submit('Create Post',['class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{{ Html::LinkRoute('posts.index','See All Posts',[],['class'=>'btn btn-outline-dark btn-block']) }}
					</div>
				</div>
			</div>

			<div class="mt-3">
				<div id="myImage-1" style="display:none">
					{-- Used in Edit only --}
				</div>
				<div id="myImage-2" style="display:none">
					{-- Uploading image will be rendered here --}
				</div>
			</div>

		{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/select2.min.js') !!}

	<script type="text/javascript">
		$('.select2-multi').select2();		
	</script>

	<script>
		// ========================================================================== //
		// Manipulate file in browser
		$("#myFile-file").change(function() {
		    renderImage(this.files[0],'myImage-2');
		});
		function renderImage(file,tagID) {
			var reader = new FileReader();
			reader.onload = function(event) {
    			the_url = event.target.result
    			$('#'+tagID).html("<img src='" + the_url + "' width=100% />")
  			}	
			reader.readAsDataURL(file);
		}

		// ========================================================================== //
		// Hide / Show elements
		function myHideShowElement(tagIDs={},op='none') {
			for (var i=0;i<tagIDs.length;i++){
				var el=document.getElementById(tagIDs[i]);
				if (el) {
					if (op=='block'){ el.style.display='block'; }
					else 			{ el.style.display='none'; }	
				}	
			}	
		}

		// ========================================================================== //
		// Flip-flop image controls and display
		function myImage(op='reset') {
			myHideShowElement(['myReset','myImage-2']);
			myHideShowElement(['myfile'],'block');
			document.getElementById('myFile-file').value="";
			document.getElementById('myFile-label').innerHTML="Select a file...";
		}

		// ========================================================================== //
		// Put the selected filename into Form::file  
		$('.custom-file-input').on('change', function(maxSize='256') {
    		let fileName = $(this).val().split('\\').pop();
    		var maxSize = 50;
    		if (fileName.length>maxSize+3) {
    			var part = parseInt(maxSize/2);
    			fileName=fileName.substr(0,part)+'...'+fileName.substr(fileName.length-part,part);
    		}
    		$(this).siblings('.custom-file-label').addClass("selected").html(fileName);
			myHideShowElement(['myReset','myImage-2'],'block');
		});
	</script>
@endsection