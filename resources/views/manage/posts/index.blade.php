@extends('manage')

@section('title','| Manage Posts')

@section('stylesheets')
@endsection

@section('content')
	@if($posts)
		<div class="row">
			<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
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
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark" style="color:inherit;">
						<th class="thleft">
							<a href="{{ route('posts.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
								<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('posts.index', ['sort'=>'t'.$sort, 'search'=>$search]) }}">
								<i id="sort-t" class="ml-3"></i>Title
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('posts.index', ['sort'=>'e'.$sort, 'search'=>$search]) }}">
								<i id="sort-e" class="ml-3"></i>Excerpt
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('posts.index', ['sort'=>'g'.$sort, 'search'=>$search]) }}">
								<i id="sort-g" class="ml-3"></i>Category
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('posts.index', ['sort'=>'a'.$sort, 'search'=>$search]) }}">
								<i id="sort-a" class="ml-3"></i>Author
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('posts.index', ['sort'=>'p'.$sort, 'search'=>$search]) }}">
								<i id="sort-p" class="ml-3"></i>Published
							</a>	
						</th>
						<th width="130px">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($posts as $post)
							<tr>
								<th>{{ $post->id }}</th>
								<td>{{ myTrim($post->title, 32) }}</td>
								<td>
									{{ myTrim($post->excerpt, 32) }}
								</td>
								<td>
									<a href="{{ route('categories.show', [$post->category_id, session('zone')]) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
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
										<span class="text-danger">{{ $status_list[$post->status] }}</span>
									@endif	
								</th>
								<td class="text-right" nowrap>
									<a href="{{ route('posts.show', $post->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('posts.edit', $post->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $posts->appends(Request::only(['search', 'sort']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
	{!! Html::script('js/app.js')     !!}
	{!! Html::script('js/helpers.js') !!}

	<script>
		mySortArrow({!! json_encode($sort) !!});
	</script>
@endsection
