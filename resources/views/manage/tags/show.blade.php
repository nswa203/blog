@extends('manage')

@section('title',"| Manage View Tag")

@section('stylesheets')
@endsection

@section('content')
	@if($tag)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-tag mr-4"></span>View {{ $tag->name }} Tag</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $tag->name !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('tags.show', $tag->id) }}">{{ route('tags.show', $tag->id) }}</a></dd>
						<dt class="col-sm-5">Tag ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('tags.show', $tag->id) }}">{{ $tag->id }}</a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($tag->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($tag->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('tags.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$tag->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['tags.delete', $tag->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('tags.index', '<i class="fas fa-tag mr-2"></i>See All Tags', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card card-body bg-light">
				<h1>
					Posts
					<span class="h1-suffix">(This Tag has {{ $tag->posts->count()==1 ? '1 Post' : $tag->posts->count().' Posts' }} assigned.)</span>
				</h1>
					<table class="table table-hover">
						<thead class="thead-dark">
							<th>#</th>
							<th>Title</th>
							<th>Excerpt</th>
							<th width="120px">Updated At</th>
							<th width="130px" class="text-right">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
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
@endsection

@section('scripts')
@endsection
