{{-- Included in 	users.show.blade					
--}}

@if($count && $albums)
	<div class="row mt-3" id="accordiona">
		<div class="col-md-12 myWrap">
			<div class="card card-body bg-light">
			<h1>
				Albums
				<span class="h1-suffix">(This {{ $zone }} has {{ $count ==1 ? '1 Album' : $count.' Albums' }} assigned.)</span>
				<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
				 		 data-toggle="collapse" data-target="#collapsea">
			 	</span></a>						
			</h1>
				<div id="collapsea" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordiona">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Title</th>
							<th>Slug</th>
							<th>Category</th>
							<th>Author</th>
							<th>#P</th>
							<th width="120px">Published</th>
							<th width="130px" class="text-right">Page {{$albums->currentPage()}} of {{$albums->lastPage()}}</th>
						</thead>
						<tbody>						
							@foreach($albums as $album)
								<tr>
									<th>{{ $album->id }}</th>
									<td>{{ myTrim($album->title, 32) }}</td>
									<td><a href="{{ url($album->slug) }}">{{ myTrim($album->slug, 32) }}</a></td>
									<td>
										<a href="{{ route('categories.show', [$album->category_id, 'Albums']) }}"><span class="badge badge-info">{{ $album->category->name }}</span></a>
									</td>
									<td>
										@if($album->user->id)
											<a href="{{ route('users.show', $album->user->id) }}">{{ $album->user->name }}</a>
										@endif	
									</td>
									<td>{{ $album->photos->count() }}</td>
									<th>
										@if($album->published_at)
											<span class="text-success">{{ date('j M Y', strtotime($album->published_at)) }}</span>
										@else	
											<span class="text-danger">{{ $status_list[$album->status] }}</span>
										@endif	
									</th>
									<td class="text-right" nowrap>
										<a href="{{ route('albums.show', $album->id)}}" class="btn btn-sm btn-outline-dark">View Album</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $albums->appends(Request::all())->render() }} 
					</div>
				</div>	
			</div>
		</div>
	</div>
@endif
