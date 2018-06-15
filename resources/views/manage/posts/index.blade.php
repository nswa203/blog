@extends('manage')

@section('title','| Manage Posts')

@section('stylesheets')
@endsection

@section('content')
	@if($posts)
		<div class="row">
			<div class="col-md-9">
				<h1><a id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-file-alt mr-4"></span>
					@endif 				 Manage Posts
				</a></h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('posts.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Create New Post</a>
			</div>
			<div class="col-md-12">
				<hr>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Title</th>
						<th>Excerpt</th>
						<th>Category</th>
						<th>Author</th>
						<th width="120px">Published</th>
						<th width="130px">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($posts as $post)
							<tr>
								<th>{{ $post->id }}</th>
								<td>{{ $post->title }}</td>
								<td>{{ substr(strip_tags($post->excerpt),0,156) }}{{ strlen(strip_tags($post->excerpt))>156 ? '...' : '' }}</td>
								<td>
									<a href="{{ route('categories.show', $post->category_id) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
								</td>
								<td>
									@if($post->user->id)
										<a href="{{ route('users.show', $post->user->id) }}">{{ $post->user->name }}</a>
									@endif	
								</td>
								<th>
									@if($post->published_at)
										<span class="text-success">{{ date('j M Y', strtotime($post->published_at)) }}</span>
									@else	
										<span class="text-danger">{{ $posts->status_names[$post->status] }}</span>
									@endif	
								<th>
									<a href="{{ route('posts.show', $post->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('posts.edit', $post->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $posts->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
