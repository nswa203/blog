{{-- Included in 	users.show.blade
					users.edit.blade
					users.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ url('manage/pu/'.$user->name) }}">{{ url('manage/pu/'.$user->name) }}</a></dd>
	<dt class="col-sm-5">Profile:</dt>
	<dd class="col-sm-7">
		@if($user->profile['id'])
			<a href="{{ route('profiles.show', $user->profile['id']) }}">{{ $user->profile['username'] }}</a>
		@endif
	</dd>							
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
