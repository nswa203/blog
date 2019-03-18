@extends('manage')

@section('title','| Manage Permissions')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="fas fa-user-cog mr-4"></span>
				@endif 				 Manage Permissions
			</a></h1>
		</div>
		<div class="col-md-3">
			<a href="{{ route('permissions.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><span class="fas fa-user-plus mr-2"></span>Create New Permission</a>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
	</div>

	@if($permissions)
		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark" style="color:inherit;">
						<th class="thleft" width="40px">
							<a href="{{ route('permissions.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
								<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('permissions.index', ['sort'=>'n'.$sort, 'search'=>$search]) }}">
								<i id="sort-n" class="ml-2"></i>Name
							</a>	
						</th>
{{--						<th class="thleft">
							<a href="{{ route('permissions.index', ['sort'=>'s'.$sort, 'search'=>$search]) }}">
								<i id="sort-s" class="ml-2"></i>Slug
							</a>	
						</th>
--}}					<th class="thleft">
							<a href="{{ route('permissions.index', ['sort'=>'d'.$sort, 'search'=>$search]) }}">
								<i id="sort-d" class="ml-2"></i>Description
							</a>	
						</th>					
						<th class="thleft" width="120px">
							<a href="{{ route('permissions.index', ['sort'=>'c'.$sort, 'search'=>$search]) }}">
								<i id="sort-c" class="ml-2"></i>Created
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('permissions.index', ['sort'=>'u'.$sort, 'search'=>$search]) }}">
								<i id="sort-u" class="ml-2"></i>Updated
							</a>	
						</th>
						<th width="130px" class="text-right">Page {{$permissions->currentPage()}} of {{$permissions->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($permissions as $permission)
							<tr>
								<th>{{ $permission->id }}</th>
								<td>{{ $permission->display_name }}</td>
{{--							<td>{{ $permission->name }}</td>
--}}							<td>{{ myTrim($permission->description, 80) }}</td>
								<td>{{ date('j M Y', strtotime($permission->created_at)) }}</td>
								<td>{{ date('j M Y', strtotime($permission->updated_at)) }}</td>
								<td class="text-right" nowrap>
									<a href="{{ route('permissions.show', $permission->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('permissions.edit', $permission->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $permissions->appends(Request::only(['search']))->render() }} 
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
