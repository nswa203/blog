@extends('main')

@section('title','| Edit Comment')

@section('stylesheets')
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
	@if($comment)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="fas fa-edit mr-4"></span>Edit Comment</h1>
				<hr>
				{!! Form::model($comment,['route'=>['comments.update',$comment->id],'method'=>'PUT']) !!}

				{{ Form::label('name','Name:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('name',null,['class'=>'form-control','disabled'=>'']) }}

				{{ Form::label('email','eMail:',['class'=>'font-bold form-spacing-top']) }}
				{{ Form::text('email',null,['class'=>'form-control','disabled'=>'']) }}

				{{ Form::label('comment','Comment:',['class'=>'font-bold form-spacing-top']) }}
				<div class="float-right form-spacing-top">
					{{ Form::checkbox('approved', '1', null) }}
					{{ Form::label('approved','Approved',['class'=>'font-bold ml-2']) }}
				</div>			

				{{ Form::textarea('comment',null,['class'=>'form-control']) }}
			</div>

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">Post URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('blog.single',$post->slug) }}">{{ route('blog.single',$post->slug) }}</a></dd>
						<dt class="col-sm-5">Comment ID</dt>
						<dd class="col-sm-7">{{ $comment->id }}</dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($comment->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($comment->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-12">
							{{ Form::submit('Save Changes',['class'=>'btn btn-success btn-block']) }}
							{!! Html::LinkRoute('posts.show','Cancel',[$post->id],['class'=>'btn btn-outline-dark btn-block']) !!}
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
@endsection