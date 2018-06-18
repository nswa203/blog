@extends('manage')

@section('title',"| Manage View Category")

@section('stylesheets')
@endsection

@section('content')
	@if($category)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-list-alt mr-4"></span>View {{ $category->name }} Category</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $category->name !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ route('categories.show', $category->id) }}</a></dd>
						<dt class="col-sm-5">Category ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ $category->id }}</a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($category->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($category->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('categories.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$category->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['categories.delete', $category->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('categories.index', '<i class="fas fa-list-alt mr-2"></i>See All Categories', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		@if($albums)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Albums
						<span class="h1-suffix">(This Category has {{ $category->albums->count()==1 ? '1 Album' : $category->albums->count().' Albums' }} assigned.)</span>
					</h1>
						<table class="table table-hover">
							<thead class="thead-dark">
								<th>#</th>
								<th>Title</th>
								<th>Description</th>
								<th width="120">Updated At</th>
								<th width="130" class="text-right">Page {{$albums->currentPage()}} of {{$albums->lastPage()}}</th>
							</thead>
							<tbody>						
								@foreach($albums as $album)
									<tr>
										<th>{{ $album->id }}</th>
										<td>{{ $album->title }}</td>
										<td>{{ substr(strip_tags($album->description),0,156) }}{{ strlen(strip_tags($album->description))>156 ? '...' : '' }}</td>
										<td>{{ date('j M Y', strtotime($album->updated_at)) }}</td>
										<td class="text-right">
											<a href="{{ route('albums.show', $album->id)}}" class="btn btn-sm btn-outline-dark">View Album</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="d-flex justify-content-center">
							{!! $albums->render() !!} 
						</div>
					</div>
				</div>
			</div>
		@endif

		@if($posts)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Posts
						<span class="h1-suffix">(This Category has {{ $category->posts->count()==1 ? '1 Post' : $category->posts->count().' Posts' }} assigned.)</span>
					</h1>
						<table class="table table-hover">
							<thead class="thead-dark">
								<th>#</th>
								<th>Title</th>
								<th>Excerpt</th>
								<th width="120">Updated At</th>
								<th width="130" class="text-right">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
							</thead>
							<tbody>						
								@foreach($posts as $post)
									<tr>
										<th>{{ $post->id }}</th>
										<td>{{ $post->title }}</td>
										<td>{{ substr(strip_tags($post->excerpt),0,156) }}{{ strlen(strip_tags($post->excerpt))>156 ? '...' : '' }}</td>
										<td>{{ date('j M Y', strtotime($post->updated_at)) }}</td>
										<td class="text-right">
											<a href="{{ route('posts.show', $post->id)}}" class="btn btn-sm btn-outline-dark">View Post</a>
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
			</div>
		@endif
	@endif
@endsection

@section('scripts')
@endsection
