@extends('main')

@section('title','| Edit Post')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
	{!! Html::style('css/select2.min.css') !!}	
@endsection

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="fas fa-edit mr-4"></span>Edit Post</h1>
				<hr>
				{!! Form::model($post,['route'=>['posts.update',$post->id],'method'=>'PUT']) !!}

				{{ Form::label('title','Title:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('title',null,['class'=>'form-control form-control-lg']) }}

				{{ Form::label('slug','Slug:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('slug',null,['class'=>'form-control','data-parsley-required'=>'','data-parsley-maxlength'=>'191','data-parsley-minlength'=>'5']) }}

				{{ Form::label('category_id','Category:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('category_id',$categories,null,['class'=>'form-control custom-select','data-parsley-required'=>'']) }}

				{{ Form::label('tags','Tags:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::select('tags[]',$tags,null,['class'=>'form-control select2-multi','multiple'=>'']) }}

				{{ Form::label('body','Body:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::textarea('body',null,['class'=>'form-control']) }}
			</div>

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('blog.single',$post->slug) }}">{{ route('blog.single',$post->slug) }}</a></dd>
						<dt class="col-sm-5">Category:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show',$post->category->id) }}"><span class="badge badge-default">{{ $post->category->name }}</span></a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($post->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($post->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::LinkRoute('posts.show','Cancel',[$post->id],['class'=>'btn btn-danger btn-block']) !!}
						</div>
						<div class="col-sm-6">
							{{ Form::submit('Save Changes',['class'=>'btn btn-success btn-block']) }}
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
	@endif
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/select2.min.js') !!}

	<script type="text/javascript">
		$('.select2-multi').select2();		
	</script>
@endsection