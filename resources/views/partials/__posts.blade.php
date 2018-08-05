{{-- Included in 	users.show.blade					
--}}

@if($count && $posts)
	<div class="row mt-3" id="accordionp">
		<div class="col-md-12 myWrap">
			<div class="card card-body bg-light">
				<h1>
					Posts
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 Post' : $count.' Posts' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapsep">
				 	</span></a>							
				</h1>
				<div id="collapsep" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionp">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Title</th>
							<th>Excerpt</th>
							<th>Category</th>
							<th>Author</th>
							<th width="120px">Published</th>
							<th class="text-right" width="130px">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
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
										<a href="{{ route('categories.show', [$post->category_id, 'Posts']) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
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
										<a href="{{ route('posts.show', $post->id)}}" class="btn btn-sm btn-outline-dark">View Post</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $posts->appends(Request::all())->render() }} 
					</div>
				</div>	
			</div>
		</div>
	</div>
@endif
