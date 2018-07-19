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
				<li>{{ $msg }}</li>
			@endforeach
		</ul>
	</div>
@endif

@if(session()->has('msgx'))
	@if(array_key_exists('failure', Session::get('msgx')))
		<div class="alert alert-danger mt-4" role="alert"><strong>Error:</strong><ul>
			@foreach(Session::get('msgx')['failure'] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>
	@endif	
	@if(array_key_exists('warning', Session::get('msgx')))
		<div class="alert alert-warning mt-4" role="alert"><strong>Warning:</strong><ul>
			@foreach(Session::get('msgx')['warning'] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>
	@endif	
	@if(array_key_exists('success', Session::get('msgx')))
		<div class="alert alert-success mt-4" role="alert"><strong>OK:</strong><ul>
			@foreach(Session::get('msgx')['success'] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>
	@endif	
	@if(array_key_exists('primary', Session::get('msgx')))
		<div class="alert alert-primary mt-4" role="alert"><strong></strong><ul>
			@foreach(Session::get('msgx')['primary'] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>
	@endif	
	@if(array_key_exists('secondary', Session::get('msgx')))
		<div class="alert alert-secondary mt-4" role="alert"><strong></strong><ul>
			@foreach(Session::get('msgx')['secondary'] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>
	@endif	
	@if(array_key_exists('info', Session::get('msgx')))
		<div class="alert alert-info mt-4" role="alert"><strong>Info:</strong><ul>
			@foreach(Session::get('msgx')['info'] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>
	@endif
	@foreach(array_diff_key(Session::get('msgx'), ['failure'=>'', 'warning'=>'', 'success'=>'', 'primary'=>'', 'secondary'=>'', 'info'=>'']) as $type => $msgs)
		<div class="alert alert-dark mt-4" role="alert"><strong>{{ $type }}:</strong><ul>
			@foreach(Session::get('msgx')[$type] as $msg)
				<li>{!! $msg !!}</li>
			@endforeach
		</ul></div>	
	@endforeach
	{{ Session::put('msgx', []) }}
@endif
