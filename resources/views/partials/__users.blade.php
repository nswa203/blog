{{-- Included in 	permissions.show.blade					
--}}

@if($count)
	<div class="row mt-3">
		<div class="col-md-12">
			<div class="card card-body bg-light">
				<h1>
					Users
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 User' : $count.' Users' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapseu">
				 	</span></a>								
				</h1>
				<div id="collapseu" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionu">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Name</th>
							<th>eMail</th>
							<th>Username</th>
							<th width="120px">Created</th>
							<th width="120px">Updated</th>
							<th width="130px" class="text-right">Page {{$users->currentPage()}} of {{$users->lastPage()}}</th>
						</thead>
						<tbody>	
							@foreach($users as $user)
								<tr>
									<th>{{ $user->id }}</th>
									<td>{{ $user->name }}</td>
									<td>{{ $user->email }}</td>
									<td>
										@if($user->profile['id'])
											<a href="{{ route('profiles.show', $user->profile['id']) }}">{{ $user->profile['username'] }}</a>
										@else
											{{ $user->profile['username'] }}
										@endif
									</td>
									<td>{{ date('j M Y', strtotime($user->created_at)) }}</td>
									<td>{{ date('j M Y', strtotime($user->updated_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('users.show', $user->id)}}" class="btn btn-sm btn-outline-dark">View User</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $users->appends(Request::all())->render() }} 
					</div>
				</div>
			</div>
		</div>
	</div>
@endif	
