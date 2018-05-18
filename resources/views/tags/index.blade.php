@extends('main')

@section('title','| All Tags')

@section('content')
	@if($tags)
		<div class="row">
			<div class="col-md-12">
				<h1><span class="fas fa-tag mr-4"></span>Tags</h1>
				<hr>
				<table class="table table-hover">
					<thead class="thead-dark">
						<th width="20px">#</th>
						<th>Name</th>
						<th width="120px">Created At</th>
						<th width="120px">Updated At</th>
						<th width="140px">Page {{$tags->currentPage()}} of {{$tags->lastPage()}}</th>
					</thead>

					<tbody>
						<tr>
							{{ Form::open(['route'=>'tags.store','method'=>'POST']) }}
								<th>+</th>
								<td>
									{{ Form::text('name',null,['class'=>'form-control form-input-primary','placeholder'=>'Create a New Tag']) }}
								</td>
								<td>{{ date('j M Y') }}</td>
								<td>{{ date('j M Y') }}</td>
								<td>
									{!! Form::submit('Create',['class'=>'btn btn-sm btn-primary btn-block']) !!}
								</td>
							{!! Form::close() !!}
						</tr>

						@foreach($tags as $tag)
							<tr>
								<th>{{ $tag->id }}</th>
								@if(Request::get('edit')==$tag->id)
									{!! Form::model($tag,['route'=>['tags.update',$tag->id],'method'=>'PUT']) !!}
										<td>{{ Form::text('name',null,['class'=>'form-control form-input-success']) }}</td>
										<td>{{ date('j M Y',strtotime($tag->created_at)) }}</td>
										<td>{{ date('j M Y',strtotime($tag->updated_at)) }}</td>
										<td>
											{!! Form::submit('Save',['class'=>'btn btn-sm btn-success']) !!}
											<a href="{{ url("/tags?page=".$tags->currentPage()) }}" class="btn btn-sm btn-outline-dark">Cancel</a>
										</td>
										{{ Form::hidden('page', $tags->currentPage()) }}
									{!! Form::close() !!}
								@else
									{!! Form::open(['route'=>['tags.destroy',$tag->id],'method'=>'DELETE']) !!}
										<td><a href="{{ route('tags.show',$tag->id) }}">{{ $tag->name }}</a></td>
										<td>{{ date('j M Y',strtotime($tag->created_at)) }}</td>
										<td>{{ date('j M Y',strtotime($tag->updated_at)) }}</td>
										<td>
											<a href="{{ url("/tags?edit=$tag->id&page=".$tags->currentPage()) }}" class="btn btn-sm btn-outline-dark">Edit</a>
											{!! Form::submit('Delete',['class'=>'btn btn-sm btn-danger']) !!}
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
