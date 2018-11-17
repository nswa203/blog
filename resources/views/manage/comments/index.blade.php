@extends('manage')

@section('title','| Manage Comments')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="far fa-comment-alt mr-4"></span>
				@endif 				 Manage Comments
			</a></h1>			
			<hr>
		</div>
	</div>		

	@if($comments)
		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark" style="color:inherit;">
						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
								<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'p'.$sort, 'search'=>$search]) }}">
								<i id="sort-p" class="ml-2"></i>Post
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'a'.$sort, 'search'=>$search]) }}">
								<i id="sort-a" class="ml-2"></i>OK
							</a>	
						</th>						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'n'.$sort, 'search'=>$search]) }}">
								<i id="sort-n" class="ml-2"></i>Name
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'e'.$sort, 'search'=>$search]) }}">
								<i id="sort-e" class="ml-2"></i>eMail
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'t'.$sort, 'search'=>$search]) }}">
								<i id="sort-t" class="ml-2"></i>Comment
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('comments.index', ['sort'=>'c'.$sort, 'search'=>$search]) }}">
								<i id="sort-c" class="ml-2"></i>Created
							</a>	
						</th>
						<th width="130px">Page {{$comments->currentPage()}} of {{$comments->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($comments as $comment)
							<tr>
								<th>{{ $comment->id }}</th>
								<td>
									<a class="font-weight-bold" href="{{ route('posts.show', $comment->post_id) }}">{{ $comment->post_id }}</a>
								</td>
								<td>
									{!! $comment->approved ? "<span class='fas fa-check text-success'></span>" : "<span class='fas fa-times text-danger'></span>" !!}
								</td>
								<td>{{ $comment->name }}</td>
								<td>{{ $comment->email }}</td>
								<td>
									{{ myTrim($comment->comment, 64) }}
								</td>
								<td>{{ date('j M Y',strtotime($comment->created_at)) }}</td>
								<td class="text-center" nowrap>
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
					{{ $comments->appends(Request::only(['search', 'sort']))->render() }} 
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
