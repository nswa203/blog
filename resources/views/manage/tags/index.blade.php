@extends('manage')

@section('title','| Manage Tags')

@section('stylesheets')
@endsection

@section('content')
	@if($tags)
		<div class="row">
			<div class="col-md-12">
				<h1><a id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-tag mr-4"></span>
					@endif 				 Manage Tags
				</a></h1>				<hr>
				
				<table class="table table-hover">
					<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Name</th>
						<th width="120px">Created At</th>
						<th width="120px">Updated At</th>
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
										<td>
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
					{{ $tags->appends(Illuminate\Support\Facades\Input::except('page'))->render() }}
				</div>

			</div>

		</div>
	@endif
@endsection

@section('scripts')
@endsection