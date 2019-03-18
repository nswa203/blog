{{-- Included in 	folders.show.blade
					folders.edit.blade
					folders.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ route('blog.folder', $folder->slug) }}">{{ route('blog.folder', $folder->slug) }}</a></dd>
	<dt class="col-sm-5">Folder ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('folders.show', $folder->id) }}">{{ $folder->id }}</a></dd>
	<dt class="col-sm-5">Category:</dt>						
	<dd class="col-sm-7">
		<a href="{{ route('categories.show', [$folder->category->id, session('zone')]) }}"><span class="badge badge-info">{{ $folder->category->name }}</span></a>
	</dd>
	<dt class="col-sm-5">Published:</dt>						
	<dd class="col-sm-7 {{ $folder->status == 1 ? 'text-success' : 'text-danger' }}">
		{{ $list['d'][$folder->status] }}
	</dd>
	<dt class="col-sm-5">Status:</dt>						
	<dd class="col-sm-7 {{ $folder->size / $folder->max_size / 1048576 > .85 ? 'text-danger' : 'text-success' }}">
		{{ $folder->max_size }}M - {{ round(($folder->size / $folder->max_size / 1048576) * 100, 2) }}% Used 
	</dd>								
	<dt class="col-sm-5">Owner:</dt>
	<dd class="col-sm-7">
		@if($folder->user->id)
			<a href="{{ route('users.show', $folder->user->id) }}">{{ $folder->user->name }}</a>
		@endif
	</dd>		
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($folder->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($folder->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
