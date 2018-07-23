{{-- Included in 	roles.show.blade
					roles.edit.blade
					roles.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ route('roles.show', $role->id) }}</a></dd>
	<dt class="col-sm-5">Role ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ $role->id }}</a></dd>							
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
