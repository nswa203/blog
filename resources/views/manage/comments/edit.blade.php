@extends('manage')

@section('title','| Manage Edit Comment')

@section('stylesheets')
	{!! Html::style('css/parsley.css') 		!!}
@endsection

@section('content')
	@if($comment)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="far fa-comment-alt mr-4"></span>Edit Comment</h1>
				<hr>
				{!! Form::model($comment, ['route'=>['comments.update',$comment->id], 'method'=>'PUT', 'data-parsley-validate'=>'']) !!}

				{{ Form::label('name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('name', null, ['class'=>'form-control', 'disabled'=>'']) }}

				{{ Form::label('email', 'eMail:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('email', null, ['class'=>'form-control', 'disabled'=>'']) }}

				{{ Form::label('comment','Comment:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::textarea('comment', null, ['class'=>'form-control', 'id'=>'textarea-body', 'data-parsley-required'=>'', 'autofocus'=>'']) }}
			</div>

			<div class="col-md-4">
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
					<dt>
						<label for="approved1">
							{{ Form::checkbox('approved', '1', null, ['class'=>'font-bold', 'hidden'=>'', 'id'=>'approved1']) }}
							<span class="span"> Approve This Comment</span>
						</label>
					</dt>
					
					<hr class="hr-spacing-top mt-2">
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
							{!! Html::decode(link_to_route('comments.index', '<i class="fas fa-comment-alt mr-2"></i>See All Comments', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
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
	{!! Html::script('js/tinymce.min.js') !!}

	<script>
		tinymce.init ({
			selector: '#textarea-body',
			plugins: "link lists",
			menubar: false,
			toolbar: "",
			forced_root_block : 'div',
			auto_focus : 'textarea-body',
 		});
	</script>
@endsection
