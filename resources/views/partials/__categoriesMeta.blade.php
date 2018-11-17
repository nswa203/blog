{{-- Included in 	categories.show.blade
					categories.delete.blade					
--}}

<dl class="row dd-nowrap">
	<dt class="col-sm-5">URL:</dt>
	<dd class="col-sm-7"><a href="{{ url('blog?pca='.$category->name) }}">{{ url('blog?pca='.$category->name) }}</a></dd>
	<dt class="col-sm-5">Category ID:</dt>
	<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ $category->id }}</a></dd>
	<dt class="col-sm-5">Created:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($category->created_at)) }}</dd>
	<dt class="col-sm-5">Last Updated:</dt>
	<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($category->updated_at)) }}</dd>
</dl>

<hr class="hr-spacing-top">
