@extends('main')

@section('title','| Tests Index')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
	{!! Html::style('css/select2.min.css') !!}
	{!! Html::script('js/tinymce.min.js') !!}
	<script>
		tinymce.init ({
			selector: 'textarea',
			plugins: "link lists",
			menubar: false,
			toolbar: ""
 		});
	</script>
	<script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><span class="fas fa-vial mr-4"></span>Tests Index</h1>
			<hr>
			
			{!! Form::open(['route'=>'tests.store','data-parsley-validate'=>'']) !!}
			{!! Form::close() !!}

			<div id="app">
				<div class="container">
					<folders></folders>
				</div>

				<div class="container">
					<images></images>
				</div>

				<div class="container">
					<slugwidget></slugwidget>
				</div>

			</div>

		</div>	
	</div>	
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/select2.min.js') !!}

	<script src="{{ asset('js/app.js') }}"></script>
@endsection	
