@extends('manage')

@section('title','| Manage Folders')

@section('stylesheets')
@endsection

@section('content')
	@if($folders)
		<div class="row">
			<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search      mr-4"></span>
					@else 				 <span class="fas fa-folder-open mr-4"></span>
					@endif 				 Manage Folders
				</a></h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('folders.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Create New Folder</a>
			</div>
			<div class="col-md-12">
				<hr>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 myWrap">
				<h1>
					<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
						data-toggle="collapse" data-target=".collapsef" onClick="myView('folder', 'accordionf2')">
				 	</span></a>
				</h1> 	
			</div>
		</div>

		<div class="row mt-3" id="accordionf">
			<div class="col-md-12 myWrap">
				<div id="accordionf1" class="collapse collapsef show" data-parent="#accordionf">
					<table class="table table-hover table-responsive-lg wrap-string">
						<thead class="thead-dark" style="color:inherit;">
							<th class="thleft" width="40px">
								<a href="{{ route('folders.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
									<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
								</a>	
							</th>
							<th class="thleft">
								<a href="{{ route('folders.index', ['sort'=>'n'.$sort, 'search'=>$search]) }}">
									<i id="sort-n" class="ml-2"></i>Name
								</a>	
							</th>
							<th class="thleft">
								<a href="{{ route('folders.index', ['sort'=>'s'.$sort, 'search'=>$search]) }}">
									<i id="sort-s" class="ml-2"></i>Slug
								</a>	
							</th>
							<th class="thleft">
								<a href="{{ route('folders.index', ['sort'=>'c'.$sort, 'search'=>$search]) }}">
									<i id="sort-c" class="ml-2"></i>Category
								</a>	
							</th>
							<th class="thleft">
								<a href="{{ route('folders.index', ['sort'=>'o'.$sort, 'search'=>$search]) }}">
									<i id="sort-o" class="ml-2"></i>Owner
								</a>	
							</th>
							<th class="thleft" width="80px">
								<a href="{{ route('folders.index', ['sort'=>'m'.$sort, 'search'=>$search]) }}">
									<i id="sort-m" class="ml-2"></i>Size
								</a>	
							</th>
							<th>Used</th>
							<th class="thleft" width="80px">
								<a href="{{ route('folders.index', ['sort'=>'f'.$sort, 'search'=>$search]) }}">
									<i id="sort-f" class="ml-2"></i>Status
								</a>	
							</th>
							<th class="thleft" width="120px">
								<a href="{{ route('folders.index', ['sort'=>'u'.$sort, 'search'=>$search]) }}">
									<i id="sort-u" class="ml-2"></i>Updated
								</a>	
							</th>
							<th width="130px">Page {{ $folders->currentPage() }} of {{ $folders->lastPage() }}</th>
						</thead>
						<tbody>
							@foreach($folders as $folder)
								<tr>
									<th>{{ $folder->id }}</th>
									<td>{{ myTrim($folder->name, 32) }}</td>
									<td><a href="{{ route('blog.folder', [$folder->slug]) }}">{{ myTrim($folder->slug, 32) }}</a></td>
									<td>
										<a href="{{ route('categories.show', [$folder->category_id, session('zone')]) }}"><span class="badge badge-info">{{ $folder->category->name }}</span></a>
									</td>
									<td>
										<a href="{{ route('users.show', $folder->user_id) }}">{{ $folder->user->name }}</a>
									</td>
									<td>{{ mySize($folder->max_size, 'M') }}</td>
									<td class="{{ $folder->size / $folder->max_size / 1048576 > .85 ? 'text-danger' : 'text-success' }}">
										{!! round(($folder->size / $folder->max_size / 1048576) * 100, 2) !!}%
									</td>
									<td class="{{ $folder->status == 1 ? 'text-success' : 'text-danger' }}">
										{{ $list['d'][$folder->status] }}
									</td>
									<td>{{ date('j M Y',strtotime($folder->updated_at)) }}</td>
									<td class="text-right" nowrap>
										<a href="{{ route('folders.show', $folder->id) }}" class="btn btn-sm btn-outline-dark">View</a>
										<a href="{{ route('folders.edit', $folder->id) }}" class="btn btn-sm btn-outline-dark">Edit</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>	           		
	            </div>

				<div id="accordionf2" class="collapse collapsef mb-2" data-parent="#accordionf">
					<div class="row ml-0 mr-0 mb-4">
						@foreach($folders as $folder)
							<div class="col-md-3 img-frame-lg" style="padding:10px 10px 15px 10px; background-color:gray;">
								<div class="text-center">
									{{ myTrim($folder->name, 32) }}
									({{ $folder->files->count() }} {{ $folder->files->count() == 1 ? 'file)' : 'files)' }}				
									<a href="{{ route('files.indexOf', [$folder->id]) }}">
										<img src="{{ route('folders.getFolderFile', [$folder->id, 'Folder.jpg']) }}"
											class="img-frame-lg" style="max-height:200px; max-width:100%;"
											onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
										/>
									</a>
								</div>
							</div>
						@endforeach
					</div>
				</div>

				<div class="d-flex justify-content-center">
					{{ $folders->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
	{!! Html::script('js/helpers.js') !!}

	<script>
		// ========================================================================== //
		// Toggles the sort direction indicator on index views
        // place this at the end of your view mySortArrow({!! json_encode($sort) !!});
		mySortArrow({!! json_encode($sort) !!});
	</script>

	<script>
		// ========================================================================== //
		// Saves the view (list or lightbox) to session storage
		// Retrieves the view from session storage and sets accordion elements
		// place this at the end of your view myView(resouce_name, 'accordionf2', 'accordionf1');
		myView('folder', 'accordionf2', 'accordionf1');
	</script>
@endsection
