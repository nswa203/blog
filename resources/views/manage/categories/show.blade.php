@extends('manage')

@section('title',"| Manage View Category")

@section('stylesheets')
@endsection

@section('content')
	@if($category)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-list-alt mr-4"></span>View {{ $category->name }} Category</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $category->name !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ route('categories.show', $category->id) }}</a></dd>
						<dt class="col-sm-5">Category ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ $category->id }}</a></dd>
						<dt class="col-sm-5">Created:</dt>
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

		@include('partials.__albums',  ['count' => $category->albums->count(),  'zone' => 'Category', 'page' => 'pageA'])
		@include('partials.__folders', ['count' => $category->folders->count(), 'zone' => 'Category', 'page' => 'pageF'])
		@include('partials.__posts',   ['count' => $category->posts->count(),   'zone' => 'Category', 'page' => 'pageP'])

	@endif
@endsection

@section('scripts')
@endsection
