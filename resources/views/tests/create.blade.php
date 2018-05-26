@extends('main')

@section('title','| Add New Post')

@section('stylesheets')

@endsection

@section('content')
	<div class="row">
		<div class="col-md-8" >
			<h1><span class="fas fa-file-alt mr-4"></span>Add A New Post</h1>
			<hr>

				<div id="app">
					{!! Form::open(['route'=>'tests.store']) !!}
					<input type="text" name="title" v-model="title">
					<slugwidget url="{{ url('/') }}" subdirectory="/" :title="title" @slug-changed="updateSlug"></slugwidget>
					{!! Form::close() !!}
				</div>

		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/app.js') !!}

	<script>
		var app=new Vue({
			el: '#app',
			data: {
				title: '',
				slug: ''
			},
			methods: {
				updateSlug: function(val){
					this.slug=val
				}
			}
		});
	</script>	
@endsection