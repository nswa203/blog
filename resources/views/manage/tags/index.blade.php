@extends('manage')

@section('title','| Manage Tags')

@section('stylesheets')
@endsection

@section('content')
	@if($tags)
		<div class="row">
			<div class="col-md-12 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-tag mr-4"></span>
					@endif 				 Manage Tags
				</a></h1>				<hr>
				
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark" style="color:inherit;">
						<th class="thleft" width="20px">
							<a href="{{ route('tags.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
								<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('tags.index', ['sort'=>'n'.$sort, 'search'=>$search]) }}">
								<i id="sort-n" class="ml-2"></i>Name
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('tags.index', ['sort'=>'c'.$sort, 'search'=>$search]) }}">
								<i id="sort-c" class="ml-2"></i>Created
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('tags.index', ['sort'=>'u'.$sort, 'search'=>$search]) }}">
								<i id="sort-u" class="ml-2"></i>Updated
							</a>	
						</th>
						<th width="140px">Page {{$tags->currentPage()}} of {{$tags->lastPage()}}</th>
					</thead>

					<tbody>
						<tr class="table-grey">
							{{ Form::open(['route'=>'tags.store', 'method'=>'POST']) }}
								<th>
									{{ Form::button('<i class="fas fa-plus-circle mb-1"></i>', ['type'=>'submit', 'class'=>'btn btn-sm btn-primary btn-block']) }}
								</th>
								<td>
									{{ Form::text('name', null, ['class'=>'form-control form-input-primary', 'placeholder'=>'Create a New Tag']) }}
								</td>
								<td>{{ date('j M Y') }}</td>
								<td>{{ date('j M Y') }}</td>
								<td>
									{{ Form::button('<i class="fas fa-plus-circle mr-2"></i>Create', ['type'=>'submit', 'class'=>'btn btn-sm btn-primary btn-block']) }}
								</td>
							{!! Form::close() !!}
						</tr>

						@foreach($tags as $tag)
							<tr>
								<th>{{ $tag->id }}</th>
								@if(Request::get('edit')==$tag->id)
									{!! Form::model($tag, ['route'=>['tags.update', $tag->id], 'method'=>'PUT']) !!}
										<td>{{ Form::text('name', null, ['class'=>'form-control form-input-success', 'autofocus'=>'']) }}</td>
										<td>{{ date('j M Y', strtotime($tag->created_at)) }}</td>
										<td>{{ date('j M Y', strtotime($tag->updated_at)) }}</td>
										<td>
											{{ Form::button('<i class=""></i>Save', ['type'=>'submit', 'class'=>'btn btn-sm btn-success']) }}
											<a href="{{ url("manage/tags?page=".$tags->currentPage()) }}" class="btn btn-sm btn-outline-dark">Cancel</a>
										</td>
										{{ Form::hidden('page', $tags->currentPage()) }}
									{!! Form::close() !!}
								@else
									{!! Form::open(['route'=>['tags.delete', $tag->id], 'method'=>'GET']) !!}
										<td><a href="{{ route('tags.show', $tag->id) }}">{{ $tag->name }}</a></td>
										<td>{{ date('j M Y', strtotime($tag->created_at)) }}</td>
										<td>{{ date('j M Y', strtotime($tag->updated_at)) }}</td>
										<td class="text-right" nowrap>
											<a href="{{ url("manage/tags?edit=$tag->id&page=".$tags->currentPage()) }}" class="btn btn-sm btn-outline-dark">Edit</a>
											{!! Form::submit('Delete', ['class'=>'btn btn-sm btn-danger']) !!}
											{{ Form::hidden('page', $tags->currentPage()) }}
										</td>
									{!! Form::close() !!}
								@endif	
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $tags->appends(Request::only(['search', 'sort']))->render() }} 
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
