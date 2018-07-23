{{-- Included in 	posts.show.blade					
--}}

@if($count && $comments)
	<div class="row mt-3">
		<div class="col-md-12">
			<div class="card card-body bg-light">
				<h1>
					Comments
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 Comment' : $count.' Comments' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapsec">
				 	</span></a>								
				</h1>
				<div id="collapsec" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionc">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>OK</th>
							<th>Name</th>
							<th>eMail</th>
							<th>Comment</th>
							<th width="120px">Created</th>
							<th width="130px" class="text-right">Page {{$comments->currentPage()}} of {{$comments->lastPage()}}</th>
						</thead>
						<tbody>						
							@foreach($comments as $comment)
								<tr>
									<th>{{ $comment->id }}</th>
									<td>
										{!! $comment->approved ? "<span class='fas fa-check text-success'></span>" : "<span class='fas fa-times text-danger'></span>" !!}
									</td>
									<td>{{ $comment->name }}</td>
									<td>{{ $comment->email }}</td>
									<td>
										{{ substr(strip_tags($comment->comment), 0, 256)}}{{ strlen(strip_tags($comment->comment)) >256 ? '...' : '' }}
									</td>
									<td>{{ date('j M Y', strtotime($comment->created_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-primary">
											<span class="far fa-edit"></span>
										</a>
										<a href="{{ route('comments.delete', $comment->id) }}" class="btn btn-sm btn-danger">
											<span class="far fa-trash-alt"></span>
										</a>	
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $comments->appends(Request::all())->render() }} 
					</div>
				</div>
			</div>
		</div>
	</div>
@endif

