{{-- Included in 	files.show.blade
					files.edit.blade
					files.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ url('fi/'.$file->id) }}">{{ url('fi/'.$file->id) }}</a></dd>
	<dt class="col-sm-5">File ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('files.show', $file->id) }}">{{ $file->id }}</a></dd>
	<dt class="col-sm-5">Folder:</dt>
	<dd class="col-sm-7"><a href="{{ route('folders.show', $file->folder_id) }}">{{ $file->folder->name }}</a></dd>
	<dt class="col-sm-5">Published:</dt>						
	<dd class="col-sm-7">
		@if($file->published_at)
			<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
				{{ date('j M Y, h:i a', strtotime($file->published_at)) }}, {{ $list['d'][$file->folder->status] }}
			</span>
		@else	
			<span class="text-danger">{{ $list['f'][$file->status] }}, {{ $list['d'][$file->folder->status] }}</span>
		@endif	
	</dd>
	<dt class="col-sm-5">Owner:</dt>						
	<dd class="col-sm-7">
		@if($file->folder->user->id)
			<a href="{{ route('users.show', $file->folder->user->id) }}">{{ $file->folder->user->name }}</a>
		@endif
	</dd>
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($file->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($file->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
