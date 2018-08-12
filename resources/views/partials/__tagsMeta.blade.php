{{-- Included in 	tags.show.blade
					tags.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ url('blog?pt='.$tag->name) }}">{{ url('blog?pt='.$tag->name) }}</a></dd>
	<dt class="col-sm-5">Tag ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('tags.show', $tag->id) }}">{{ $tag->id }}</a></dd>							
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($tag->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($tag->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
