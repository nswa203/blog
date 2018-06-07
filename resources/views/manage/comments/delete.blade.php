@extends('main')

@section('title','| Delete Comment')

@section('stylesheets')
@endsection

@section('content')
	@if($comment)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="fas fa-trash-alt mr-4"></span>DELETE THIS COMMENT</h1>
				<hr>
				<div class="mt-5">
					<p><strong class="mr-3">Name:</strong> {{ $comment->name }}</p>
					<p><strong class="mr-3">eMail:</strong> {{ $comment->email }}</p>
					<p class="mt-5"><strong>Comment:</strong></p>
					<p>{{ strip_tags($comment->comment) }}</p>
				</div>
			</div>

			<div class="col-md-4">
				{!! Form::open(['route' => ['comments.destroy', $comment->id], 'method'=>'DELETE']) !!}
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">Post URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('blog.single', $post->slug) }}">{{ route('blog.single', $post->slug) }}</a></dd>
						<dt class="col-sm-5">Comment ID</dt>
						<dd class="col-sm-7"><a href="{{ route('blog.single', $post->slug) }}">{{ $comment->id }}</a></dd>						
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($comment->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($comment->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-12">
							{{ Form::submit('YES DELETE NOW', ['class'=>'btn btn-danger btn-block font-weight-bold']) }}
							<a href="{{ url()->previous() }}" class="form-control btn btn-outline-dark btn-block">Cancel</a>
						</div>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
