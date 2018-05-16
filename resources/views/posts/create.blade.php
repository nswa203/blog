@extends('main')

@section('title','| Create New Post')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
	{!! Html::style('css/select2.min.css') !!}	
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8" >
			<h1>Create New Post</h1>
			<hr>
			{!! Form::open(['route'=>'posts.store','data-parsley-validate'=>'']) !!}
				{{ Form::label('title','Title:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title',null,['class'=>'form-control form-control-lg','data-parsley-required'=>'','data-parsley-maxlength'=>'191']) }}

				{{ Form::label('slug','Slug:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('slug',null,['class'=>'form-control','data-parsley-required'=>'','data-parsley-maxlength'=>'191','data-parsley-minlength'=>'5','placeholder'=>'your-slug']) }}

				{{ Form::label('category_id','Category:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('category_id',$categories,null,['class'=>'form-control','placeholder'=>'Select a Category...','data-parsley-required'=>'']) }}

				{{ Form::label('tags','Tags:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('tags[]',$tags,null,['class'=>'form-control select2-multi','multiple'=>'']) }}

				{{ Form::label('body','Body:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::textarea('body',null,['class'=>'form-control','data-parsley-required'=>'']) }}
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row">
					<dt class="col-sm-5">URL:</dt>
					<dd class="col-sm-7"><a href="#">{{ route('blog.single','your-url') }}</a></dd>
					<dt class="col-sm-5">Category:</dt>
					<dd class="col-sm-7"><a href="#"><span class="badge badge-default">Select a Category...</span></a></dd>					
					<dt class="col-sm-5">Created At:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:ia') }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:ia') }}</dd>
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
@endsection