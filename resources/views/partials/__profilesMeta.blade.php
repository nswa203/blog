{{-- Included in 	profiles.show.blade
					profiles.edit.blade
					profiles.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ route('profiles.show', $profile->id) }}">{{ route('profiles.show', $profile->id) }}</a></dd>
	<dt class="col-sm-5">User:</dt>
	<dd class="col-sm-7"><a href="{{ route('users.show', $profile->user->id) }}">{{ $profile->user->name }}</a></dd>							
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($profile->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($profile->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
