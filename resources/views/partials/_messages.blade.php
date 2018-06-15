@if (Session::has('success'))
	<div class="alert alert-success mt-4" role="alert">
		<strong>Success:</strong> {!! Session::get('success') !!}
	</div>
	{{ session()->forget('success') }}	
@endif

@if (Session::has('failure'))
	<div class="alert alert-danger mt-4" role="alert">
		<strong>Error:</strong> {!! Session::get('failure') !!}
	</div>
	{{ session()->forget('failure') }}	
@endif

@if (count($errors)>0)
	<div class="alert alert-danger mt-4" role="alert">
		<strong>Errors:</strong>
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

@if (session()->has('msgs'))
	<div class="alert alert-info mt-4" role="alert">
		<strong>Note:</strong>
		<ul>
			@foreach(Session::get('msgs') as $msg)
				<li> {{ $msg }}</li>
			@endforeach
		</ul>
	</div>
@endif
