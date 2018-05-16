@extends('main')

@section('title','| View Post')

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8">
				<h1>{{ $post->title }}</h1>
				<hr>
				<p class="lead">{{ $post->body }}</p>
				<hr>
				<div class="tags">
					@foreach ($post->tags as $tag)
						<a href="{{ route('tags.show',$tag->id) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('blog.single',$post->slug) }}">{{ route('blog.single',$post->slug) }}</a></dd>
						<dt class="col-sm-5">Category:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show',$post->category->id) }}"><span class="badge badge-default">{{ $post->category->name }}</span></a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:ia',strtotime($post->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:ia',strtotime($post->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::LinkRoute('posts.edit','Edit',[$post->id],['class'=>'btn btn-primary btn-block']) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['posts.destroy',$post->id],'method'=>'DELETE']) !!}
							{!! Form::submit('Delete',['class'=>'btn btn-danger btn-block']) !!}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{{ Html::LinkRoute('posts.index','See All Posts',[],['class'=>'btn btn-outline-dark btn-block']) }}
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
@endsection
