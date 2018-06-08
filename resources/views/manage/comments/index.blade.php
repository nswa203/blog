@extends('manage')

@section('title','| Manage Comments')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<h1><a id="menu-toggle2">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="fas fa-comment-alt mr-4"></span>
				@endif 				 Manage Comments
			</a></h1>			
			<hr>
		</div>
	</div>		

	@if($comments)
		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="thead-dark">
						<th>#</th>
						<th>Post</th>						
						<th>OK</th>
						<th>Name</th>
						<th>eMail</th>
						<th>Comment</th>
						<th width="120px">Created At</th>
						<th width="130px">Page {{$comments->currentPage()}} of {{$comments->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($comments as $comment)
							<tr>
								<th>{{ $comment->id }}</th>
								<th>
									<a href="{{ route('posts.show', $comment->post_id) }}">{{ $comment->post_id }}</a>
								</th>
								<td>
									{!! $comment->approved ? "<span class='fas fa-check text-success'></span>" : "<span class='fas fa-times text-danger'></span>" !!}
								</td>
								<td>{{ $comment->name }}</td>
								<td>{{ $comment->email }}</td>
								<td>{{ substr(strip_tags($comment->comment),0,256)}}{{ strlen(strip_tags($comment->comment))>256?'...':'' }}</td>
								<td>{{ date('j M Y',strtotime($comment->created_at)) }}</td>

								<td class="text-center">
									<a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-primary">
										<span class="far fa-edit"></span>
									</a>
									<a href="{{ route('comments.delete', $comment->id) }}" class="btn btn-sm btn-danger">
										<span class="far fa-trash-alt"></span>
									</a>	
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{!! $comments->render() !!}
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
