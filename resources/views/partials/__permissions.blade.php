{{-- Included in 	users.show.blade					
--}}

@if($count)
	<div class="row mt-3" id="accordionpm">
		<div class="col-md-12">
			<div class="card card-body bg-light">
				<h1>
					Permissions
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 Permission' : $count.' Permissions' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapsepm">
				 	</span></a>								
				</h1>
				<div id="collapsepm" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionpm">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Name</th>
							<th>Slug</th>
							<th>Description</th>
							<th width="120px">Created</th>
							<th width="120px">Updated</th>
							<th width="130px" class="text-right">Page {{$permissions->currentPage()}} of {{$permissions->lastPage()}}</th>
						</thead>
						<tbody>						
							@foreach($permissions as $permission)
								<tr>
									<th>{{ $permission->id }}</th>
									<td>{{ $permission->display_name }}</td>
									<td>{{ $permission->name }}</td>
									<td>{{ substr($permission->description, 0, 156) }}{{ strlen($permission->description)>156 ? '...' : '' }}
									<td>{{ date('j M Y', strtotime($permission->created_at)) }}</td>
									<td>{{ date('j M Y', strtotime($permission->updated_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('permissions.show', $permission->id)}}" class="btn btn-sm btn-outline-dark">View Permission</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $permissions->appends(Request::all())->render() }}
					</div>
				</div>	
			</div>
		</div>
	</div>
@endif	
