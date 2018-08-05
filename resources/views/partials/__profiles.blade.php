{{-- Included in 	folders.show.blade					
--}}

@if($count && $profiles)
	<div class="row mt-3" id="accordionp">
		<div class="col-md-12 myWrap">
			<div class="card card-body bg-light">
				<h1>
					Profiles
					<span class="h1-suffix">(This {{ $zone }} has {{ $count == 1 ? '1 Profile' : $count.' Profiles' }} assigned.)</span>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
					 		 data-toggle="collapse" data-target="#collapsepr">
				 	</span></a>							
				</h1>
				<div id="collapsepr" class="collapse {{ request()->has($page) ? 'show' : 'hide' }}" data-parent="#accordionpr">				
					<table class="table table-hover table-responsive-lg">
						<thead class="thead-dark">
							<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
							<th>Name</th>
							<th>eMail</th>
							<th>Username</th>
							<th width="120px">Created</th>
							<th width="120px">Updated</th>
							<th class="text-right" width="130px">Page {{$profiles->currentPage()}} of {{$profiles->lastPage()}}</th>
						</thead>
						<tbody>
							@foreach($profiles as $profile)
								<tr>
									<th>{{ $profile->id }}</th>
									<td>{{ $profile->user->name }}</td>
									<td>{{ $profile->user->email }}</td>
									<td>{{ $profile->username }}</td>
									<td>{{ date('j M Y', strtotime($profile->created_at)) }}</td>
									<td>{{ date('j M Y', strtotime($profile->updated_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('profiles.show', $profile->id)}}" class="btn btn-sm btn-outline-dark">View Profile</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{{ $profiles->appends(Request::all())->render() }} 
					</div>
				</div>	
			</div>
		</div>
	</div>
@endif
