@extends('main')

@section('title',"| DELETE $category->name Category")

@section('content')
	@if($category)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="fas fa-list-alt mr-4"></span>DELETE {{ $category->name }} Category</h1>
				<h5><small>Has {{ $category->posts()->count()?$category->posts()->count():'No' }} posts</small></h5>
				<hr>
			</div>

			<div class="col-md-4">
				{!! Form::open(['route' => ['categories.destroy', $category->id], 'method'=>'DELETE']) !!}
					<div class="card card-body bg-light">
						<dl class="row">
							<dt class="col-sm-5">Category URL:</dt>
							<dd class="col-sm-7"><a href="{{ route('categories.show',$category->id) }}">{{ route('categories.show',$category->id) }}</a></dd>
							<dt class="col-sm-5">Category ID</dt>
							<dd class="col-sm-7">{{ $category->id }}</dd>							
							<dt class="col-sm-5">Created At:</dt>
							<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($category->created_at)) }}</dd>
							<dt class="col-sm-5">Last Updated:</dt>
							<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($category->updated_at)) }}</dd>
						</dl>
						<hr class="hr-spacing-top">

						<div class="row mt-3">
							<div class="col-sm-12">
								{{ Form::submit('YES DELETE NOW',['class'=>'btn btn-danger btn-block font-weight-bold']) }}
								<a href="{{ url()->previous() }}" class="form-control btn btn-outline-dark btn-block">Cancel</a>
							</div>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>

		<h3 class="posts-title">
			<span class="fas fa-file-alt mr-2"></span>
			{{ $category->posts->count()=='0' ? 'No Posts!' : ($category->posts->count()=='1' ? '1 Post' : $category->posts->count().' Posts') }}
		</h3>
		@if($category->posts->count()>0)
			<div class="row mt-3">
				<div class="col-md-12">
					<table class="table table-hover">
						<thead class="thead-dark">
							<th>#</th>
							<th>Post Title</th>
							<th>Category</th>
							<th>Tags</th>
							<th width=40px></th>
						</thead>
						<tbody>
							@foreach($category->posts as $post)
								<tr>
									<th>{{ $post->id }}</th>
									<td>{{ $post->title }}</td>
									<td><a href="{{ route('categories.show',$post->category->id) }}"><span class="badge badge-default">{{ $post->category->name }}</span></a></td>
									<td>
										@foreach ($post->tags as $category)
											<a href="{{ route('tags.show',$category->id) }}"><span class="badge badge-info">{{ $category->name }}</span></a>
										@endforeach
									</td>
									<td>
										<a href="{{ route('posts.show',$post->id) }}" class="btn btn-sm btn-outline-dark">View Post</a>
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
