{{-- Included in 	folders.show.blade
					tags.show.blade
--}}

@if($count && $files)
	<div class="row mt-3" id="accordionf">
		<div class="col-md-12 myWrap">
			<div class="card card-body bg-light">
				<h1>
					Files
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 File' : $count.' Files' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapsef">
				 	</span></a>
				</h1>
				<div id="collapsef" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionf">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Title</th>
						<th>Tags</th>
						<th>Owner</th>
						<th>Size</th>
						<th width="120px">Published</th>
						<th width="130px" class="text-right">Page {{$files->currentPage()}} of {{$files->lastPage()}}</th>
						</thead>
						<tbody>						
							@foreach($files as $file)
								<tr>
								<th>{{ $file->id }}</th>
								<td>{{ myTrim($file->title, 32) }}</td>
								<td>
									@foreach ($file->tags as $tag)
										<a href="{{ route('tags.show', [$tag->id, 'Files']) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
									@endforeach
								</td>
								<td>
									<a href="{{ route('users.show', $file->folder->user_id) }}">{{ $file->folder->user->name }}</a>
								</td>
								<td>{{ mySize($file->size) }} </td>
								<th>
									@if($file->published_at)
										<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
											{{ date('j M Y', strtotime($file->published_at)) }}, {{ $list['d'][$file->folder->status] }}
										</span>
									@else	
										<span class="text-danger">{{ $list['f'][$file->status] }}, {{ $list['d'][$file->folder->status] }}</span>
									@endif	
								</th>
									<td class="text-right" nowrap>
										<a href="{{ route('files.show', $file->id)}}" class="btn btn-sm btn-outline-dark">View File</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $files->appends(Request::all())->render() }} 
					</div>
				</div>			
			</div>
		</div>
	</div>
@endif
