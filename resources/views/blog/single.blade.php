@extends('main')

@if ($post)
	@section('title',"| $post->slug")

	@section('content')
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<h1>{{ $post->title }}</h1>
				<p>{{ $post->body }}</p>
				<hr>
				<p>Posted In: {{ $post->category->name }}</p>
			</div>
		</div>

		<div class="row">
		 	<div class="col-md-8 offset-md-2">
				<h3 class="comments-title">
					<span class="fas fa-comment-alt mr-2"></span>
					{{ $post->comments->count()=='0' ? 'No Comments yet!' : ($post->comments->count()=='1' ? '1 Comment' : $post->comments->count().' Comments') }}
				</h3>

				@foreach($post->comments as $comment)
					<div class="comment">
						<div class="author-info">
							<img src="{{ 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($comment->email))) . '?s=50&d=wavatar' }}" class="author-image">
							<div class="author-name">
								<h4>{{ $comment->name }}</h4>
								<p class="author-time">{{ date('j M Y, h:i a',strtotime($comment->created_at)) }}</p>
							</div>
						</div>
						<div class="comment-content">
							{{ $comment->comment }}
						</div>
					</div>
				@endforeach
		 	</div>
		</div> 

		<div class="row">
			<div class="col-md-8 offset-md-2 mt-5">
				{{ Form::open(['route' => ['comments.store', $post->id], 'method' => 'POST']) }}
					<div class="row">
						<div class="col-md-6">
							{{ Form::label('name', 'Your Name:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('name', null, ['class' => 'form-control']) }}
						</div>

						<div class="col-md-6">
							{{ Form::label('email', 'Your eMail:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('email', null, ['class' => 'form-control']) }}
						</div>

						<div class="col-md-12 mt-2">
							{{ Form::label('comment', 'Comment:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::textarea('comment', null, ['class' => 'form-control', 'rows' => '5', 'placeholder' => 'Include your comments here...']) }}
							{{ Form::submit('Add Your Comment', ['class' => 'btn btn-success btn-block form-spacing-top']) }}
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>

	@endsection
@endif
