{{-- Included in 	categories.show.blade
					posts.show.blade
					profiles.show.blade
					users.show.blade					
--}}

@if($count && $folders)
	<div class="row mt-3" id="accordionf">
		<div class="col-md-12">
			<div class="card card-body bg-light">
				<h1>
					Folders
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 Folder' : $count.' Folders' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapsef">
				 	</span></a>
				</h1>
				<div id="collapsef" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionf">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Name</th>
							<th>Slug</th>
							<th>Category</th>
							<th>Owner</th>
							<th>Used</th>
							<th width="120px">Updated</th>
							<th width="130px" class="text-right">Page {{$folders->currentPage()}} of {{$folders->lastPage()}}</th>
						</thead>
						<tbody>						
							@foreach($folders as $folder)
								<tr>
									<th>{{ $folder->id }}</th>
									<td>{{ $folder->name }}</td>
									<td><a href="{{ url('f/'.$folder->slug) }}">{{ $folder->slug }}</a></td>
									<td>
										<a href="{{ route('categories.show', [$folder->category_id, 'Albums']) }}"><span class="badge badge-info">{{ $folder->category->name }}</span></a>
									</td>
									<td>
										@if($folder->user->id)
											<a href="{{ route('users.show', $folder->user->id) }}">{{ $folder->user->name }}</a>
										@endif	
									</td>
									<td class="{{ $folder->size / $folder->max_size > .85 ? 'text-danger' : 'text-success' }}">
										{{ round(($folder->size / $folder->max_size) * 100, 2) }}%
									</td>
									<td>{{ date('j M Y', strtotime($folder->updated_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('folders.show', $folder->id)}}" class="btn btn-sm btn-outline-dark">View Folder</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $folders->appends(Request::all())->render() }} 
					</div>
				</div>			
			</div>
		</div>
	</div>
@endif
