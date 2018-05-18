@extends('main')

@section('title','| All Posts')

@section('content')
	@if($posts)
		<div class="row">
			<div class="col-md-9">
				<h1><span class="fas fa-file-alt mr-4"></span>All Posts</h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('posts.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing">Create New Post</a>
			</div>
			<div class="col-md-12">
				<hr>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="thead-dark">
						<th>#</th>
						<th>Title</th>
						<th>Body</th>
						<th width="120px">Created At</th>
						<th width="120px">Updated At</th>
						<th width="120px">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($posts as $post)
							<tr>
								<th>{{ $post->id }}</th>
								<td>{{ $post->title }}</td>
								<td>{{ substr(strip_tags($post->body),0,48) }}{{ strlen(strip_tags($post->body))>48?'...':'' }}</td>
								<td>{{ date('j M Y',strtotime($post->created_at)) }}</td>
								<td>{{ date('j M Y',strtotime($post->updated_at)) }}</td>
								<td>
									<a href="{{ route('posts.show',$post->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('posts.edit',$post->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{!! $posts->render() !!}
				</div>
			</div>
		</div>
	@endif
@endsection
