@extends('main')

@section('title','| Create New Post')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row mt-5">
		<div class="col-md-8 offset-md-2" >
			<h1>Create New Post</h1>
			<hr>
			
			{!! Form::open(['route'=>'posts.store','data-parsley-validate'=>'']) !!}
			
				{{ Form::label('title','Title:') }}
				{{ Form::text('title',null,['class'=>'form-control','data-parsley-required'=>'','data-parsley-maxlength'=>'191']) }}
				
				{{ Form::label('body','Post Body:') }}
				{{ Form::textarea('body',null,['class'=>'form-control','data-parsley-required'=>'']) }}
			
				{{ Form::submit('Create Post',['class'=>'btn btn-success btn-lg btn-block mt-4']) }}
				
			{!! Form::close() !!}
			
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
@endsection