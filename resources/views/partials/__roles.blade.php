{{-- Included in 	users.show.blade					
--}}

@if($count && $roles)
	<div class="row mt-3" id="accordionr">
		<div class="col-md-12">
			<div class="card card-body bg-light">
				<h1>
					Roles
					<span class="h1-suffix">(This {{ $zone }} has {{ $count ==1 ? '1 Role' : $count.' Roles' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapser">
				 	</span></a>							
				</h1>
				<div id="collapser" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionr">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Name</th>
							<th>Slug</th>
							<th>Description</th>
							<th width="120px">Created</th>
							<th width="120px">Updated</th>
							<th width="130px" class="text-right">Page {{$roles->currentPage()}} of {{$roles->lastPage()}}</th>
						</thead>
						<tbody>						
							@foreach($roles as $role)
								<tr>
									<th>{{ $role->id }}</th>
									<td>{{ $role->display_name }}</td>
									<td>{{ $role->name }}</td>
									<td>{{ substr($role->description, 0, 156) }}{{ strlen($role->description)>156 ? '...' : '' }}
									<td>{{ date('j M Y', strtotime($role->created_at)) }}</td>
									<td>{{ date('j M Y', strtotime($role->updated_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('roles.show', $role->id)}}" class="btn btn-sm btn-outline-dark">View Role</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $roles->appends(Request::all())->render() }} 
					</div>
				</div>	
			</div>
		</div>
	</div>
@endif
