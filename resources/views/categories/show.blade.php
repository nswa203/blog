@extends('main')

@section('title',"| $category->name Category")

@section('content')
	@if($category)
		<div class="row">
			<div class="col-md-8">
				<h1><span class="fas fa-list-alt mr-4"></span>{{ $category->name }} Category</h1>
				<h5><small>Has {{ $category->posts()->count()?$category->posts()->count():'No' }} posts</small></h5>
				<hr>
			</div>

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($category->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a',strtotime($category->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">

					<div class="row mt-3">
						<div class="col-sm-12">
							{{ Html::LinkRoute('categories.index','Manage All Categories',[],['class'=>'btn btn-outline-dark btn-block']) }}
						</div>
					</div>
				</div>
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
