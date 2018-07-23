{{-- Included in 	permissions.show.blade
					permissions.edit.blade
					permissions.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7">
		<a href="{{ route('permissions.show', $permission->id) }}">{{ route('permissions.show', $permission->id) }}</a>
	</dd>
	<dt class="col-sm-5">Permission ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('permissions.show', $permission->id) }}">{{ $permission->id }}</a></dd>
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
