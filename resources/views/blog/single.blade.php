@extends('main')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@if ($post)
	@section('title', "| $post->slug")

	@section('content')
		<div class="row">
			<div class="col-md-8 offset-md-2">
				@if($post->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$post->banner) }}" width="100%"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</div>
				@endif
					<a href="{{ asset('images/'.$post->image) }}">
						<img src="{{ asset('images/'.$post->image) }}" width="150px" class="img-frame float-left mr-4" style="margin-top:-10px; margin-bottom:10px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</a>
				<h1>{{ $post->title }}</h1>
				<p>{!! $post->body !!}</p>
				<hr>
				<p>
					@foreach ($post->tags as $tag)
						<span class="badge badge-info">{{ $tag->name }}</span>
					@endforeach
				</p>
				<p>
					Posted In: {{ $post->category->name }}
					<span class="float-right">Published: {{ date('j M Y, h:i a', strtotime($post->published_at)) }}</span>
				</p>
			</div>
		</div>

		<div class="row">
		 	<div class="col-md-8 offset-md-2">
				<h3 class="comments-title">
					<span class="far fa-comment-alt mr-2"></span>
					{{ $post->comments->count()=='0' ? 'No Comments yet!' : ($post->comments->count()=='1' ? '1 Comment' : $post->comments->count() . ' Comments') }}
				</h3>

				@foreach($post->comments as $comment)
					<div class="comment-{{ $loop->index % 2 == 0 ? 'evenColor' : 'oddColor' }} pt-2 pr-2 pb-2 pl-2">
						<div class="author-info">
							<img src="{{ 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($comment->email))) . '?s=50&d=wavatar' }}" class="author-image">
							<div class="author-name">
								<h4>{{ $comment->name }}</h4>
								<p class="author-time">{{ date('j M Y, h:i a', strtotime($comment->created_at)) }}</p>
							</div>
						</div>
						<div class="comment-content">
							{!! $comment->comment !!}
						</div>
					</div>
				@endforeach
		 	</div>
		</div> 

		<div class="row">
			<div class="col-md-8 offset-md-2 mt-5">
				{{ Form::open(['route'=>['comments.store', $post->id], 'method'=>'POST', 'data-parsley-validate'=>'']) }}
					<div class="row">
						<div class="col-md-6">
							{{ Form::label('name', 'Your Name:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('name', null, ['class' => 'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191']) }}
						</div>

						<div class="col-md-6">
							{{ Form::label('email', 'Your eMail:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('email', null, ['class' => 'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'5', 'data-parsley-maxlength'=>'191']) }}
						</div>

						<div class="col-md-12 mt-2">
							{{ Form::label('comment', 'Comment:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::textarea('comment', null, ['class' => 'form-control', 'rows' => '5', 'placeholder' => 'Include your comments here...', 'rows'=>'3', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'8', 'data-parsley-maxlength'=>'2048']) }}
	
							{{ Form::label('', 'Are You Human?', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::button('<h4><i class="far fa-comment-alt mr-3"></i>Add Your Comment</h4>',
								['type'=>'submit', 'class'=>'btn btn-success float-right', 'style'=>'margin-top:54px; height:75px; width:300px;']) }}
							<div class="g-recaptcha" data-sitekey="{!! env('CAPTCHA_SITEKEY') !!}"></div>
						</div>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	@endsection
@endif

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/tinymce.min.js') !!}

	<script>
		tinymce.init ({
			selector: 'textarea',
			plugins: "link lists",
			menubar: false,
			toolbar: "",
			forced_root_block : 'div',
 		});
	</script>
@endsection
