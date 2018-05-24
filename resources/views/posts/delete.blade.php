@extends('main')

@section('title','| DELETE Post')

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="fas fa-trash-alt mr-4"></span> DELETE {{ $post->title }}</h1>
				<hr>
				<p class="lead">{!! $post->body !!}</p>
				<hr>
				<div class="tags">
					@foreach ($post->tags as $tag)
						<a href="{{ route('tags.show',$tag->id) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('blog.single',$post->slug) }}">{{ route('blog.single',$post->slug) }}</a></dd>
						<dt class="col-sm-5">Category:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show',$post->category->id) }}"><span class="badge badge-default">{{ $post->category->name }}</span></a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($post->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($post->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-12">
							{!! Form::open(['route'=>['posts.destroy',$post->id],'method'=>'DELETE']) !!}
								{{ Form::submit('YES DELETE NOW',['class'=>'btn btn-danger btn-block font-weight-bold']) }}
								<a href="{{ url()->previous() }}" class="form-control btn btn-outline-dark btn-block">Cancel</a>
							{!! Form::close() !!}
						</div>
					</div>
				</div>
				@if($post->image)
					<img src="{{ asset('images/'.$post->image) }}" width="100%" class="mt-3"/>
				@endif	
			</div>
		</div>	
		
		<h3 class="comments-title">
			<span class="fas fa-comment-alt mr-2"></span>
			{{ $post->comments->count()=='0' ? 'No Comments yet!' : ($post->comments->count()=='1' ? '1 Comment' : $post->comments->count().' Comments') }}
		</h3>
		@if($post->comments->count()>0)
			<div class="row mt-3">
				<div class="col-md-12">
					<table class="table table-hover">
						<thead class="thead-dark">
							<th>#</th>
							<th>OK</th>
							<th>Name</th>
							<th>eMail</th>
							<th>Comment</th>
							<th width="120px">Created At</th>
							<th width="96px"></th>
						</thead>
						<tbody>
							@foreach($post->comments as $comment)
								<tr>
									<th>{{ $comment->id }}</th>
									<td>{{ $comment->approved?'Y':'N' }}</td>
									<td>{{ $comment->name }}</td>
									<td>{{ $comment->email }}</td>
									<td>{{ substr(strip_tags($comment->comment),0,256)}}{{ strlen(strip_tags($comment->comment))>256?'...':'' }}</td>
									<td>{{ date('j M Y',strtotime($comment->created_at)) }}</td>

									<td>
										<a href="{{ route('comments.edit',$comment->id) }}" class="btn btn-sm btn-primary">
											<span class="far fa-edit"></span>
										</a>
										<a href="{{ route('comments.delete',$comment->id) }}" class="btn btn-sm btn-danger">
											<span class="far fa-trash-alt"></span>
										</a>	
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						
					</div>
				</div>
			</div>
		@endif
	@endif
@endsection
