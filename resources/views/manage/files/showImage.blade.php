@extends('main')

@section('title','| Manage Zoom File')

@section('stylesheets')
@endsection

@section('navControls')
	@if ($list['x'])
		<div class="row lt-2">
			@for ($i=0; $i<=6; $i=$i+2)
				<div class="mr-2">
					<a href="{{ route('files.showFile', $list['x'][$i+1]) }}" class="btn btn-block btn-outline-secondary
					{{ $list['x'][$i] }} {{ $list['x'][$i+1] ? '' : 'disabled' }}"></a>
				</div>
				@if ($i==2)
					<div class="mr-2">
						<a href="{{ route('files.show', [$file->id]) }}" class="btn btn-block btn-outline-secondary
						fas fa-stop }}"></a>
					</div>
				@endif
			@endfor
		</div>
	@endif
@endsection

@section('contentLarge')
	@if($file)
		<div class="row">
			<div class="col-md-12 myWrap">

				
			

					@if(substr($file->mime_type, 0, 2) == 'au' or substr($file->mime_type, 0, 2) == 'vi' or $file->ext == 'mp3')
						@if((substr($file->mime_type, 0, 2) == 'au' or $file->ext == 'mp3') && isset($meta))
							<div class="text-center mb-4" >
								<h4>
									{{ isset($meta->Performer) ? $meta->Performer . ' /' : '' }}
									{{ isset($meta->Album    ) ? $meta->Album     . ' /' : '' }}
									{{ isset($meta->Title    ) ? $meta->Title            : '' }}
								<h4>
							</div>
						@endif				
						<a href="{{ route('files.show', [$file->id]) }}">
							<div style="object-fit:contain; margin:0 auto; height:91vh;	width:{{ substr($file->mime_type, 0, 2) == 'vi' ? '100%' : '50%' }};">
								<video controls poster="{{
										isset($meta->Picture) ? 'data:image/jpeg;base64,' . $meta->Picture :
										(substr($file->mime_type, 0, 2) == 'au' ? asset('favicon.ico') : '') }}">
				    				<source src="{{ route('private.getFile', [$file->id]) }}" type="{{ $file->mime_type }}" />
				    				<source src="{{ route('private.getFile', [$file->id]) }}" type="video/mp4" />
								</video>
							</div>
						</a>	
					@elseif(substr($file->mime_type, 0, 2) == 'im')
						<a href="{{ route('files.show', [$file->id]) }}">
							<img src    ="{{ route('private.getFile', [$file->id]) }}"
								 style  ="object-fit:contain; width:100%; height:91vh;"
								 onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
						</a>
					@else
						<div class="text-center">
							<a href="{{ route('private.getFile', [$file->id]) }}">
								Click to Open {{ $file->mime_type }}
								<img src="{{ route('private.getFile', [$file->id]) }}"
								 style  ="object-fit:contain; width:100%; height:91vh;"
								 onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
							</a>
						</div>		
 					@endif

				</a>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
