@extends('main')

@section('title','| Tests Show')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><span class="fas fa-vial mr-4"></span>Tests Show {{ $test }}</h1>
			<hr>
						
			<div id="app">
				<folders></folders>
				<images></images>
			</div>
			
			<div>
				
			</div>	
		
		</div>	
	</div>	
@endsection

@section('scripts')
	{!! Html::script('js/app.js') !!}
	<script>
		var app=new Vue({
			el: '#app',
		});
	</script>	
@endsection