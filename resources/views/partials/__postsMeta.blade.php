{{-- Included in 	posts.show.blade
					posts.edit.blade
					posts.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	
	<dd class="col-sm-7"><a href="{{ route('blog.single', $post->slug) }}">{{ url($post->slug) }}</a></dd>

	<dt class="col-sm-5">Post ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('posts.show', $post->id) }}">{{ $post->id }}</a></dd>
	<dt class="col-sm-5">Category:</dt>						
	<dd class="col-sm-7">
		<a href="{{ route('categories.show', [$post->category->id, session('zone')]) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
	</dd>
	<dt class="col-sm-5">Published:</dt>						
	<dd class="col-sm-7">
		@if($post->published_at)
			{{ date('j M Y, h:i a', strtotime($post->published_at)) }}
		@else	
			<span class="text-danger">{{ $status_list[$post->status] }}</span>
		@endif	
	</dd>							
	<dt class="col-sm-5">Author:</dt>
	<dd class="col-sm-7">
		@if($post->user->id)
			<a href="{{ route('users.show', $post->user->id) }}">{{ $post->user->name }}</a>
		@endif
	</dd>		
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">