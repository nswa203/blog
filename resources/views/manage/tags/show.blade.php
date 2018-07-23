@extends('manage')

@section('title',"| Manage View Tag")

@section('stylesheets')
@endsection

@section('content')
	@if($tag)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-tag mr-4"></span>View {{ $tag->name }} Tag</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $tag->name !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('tags.show', $tag->id) }}">{{ route('tags.show', $tag->id) }}</a></dd>
						<dt class="col-sm-5">Tag ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('tags.show', $tag->id) }}">{{ $tag->id }}</a></dd>							
						<dt class="col-sm-5">Created:</dt>
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

		@include('partials.__albums', ['count' => $tag->albums->count(), 'zone' => 'Tag', 'page' => 'pageA'])
		@include('partials.__posts',  ['count' => $tag->posts->count(),  'zone' => 'Tag', 'page' => 'pageP'])

		@if($tag->photos->count() && $photos)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Photos
						<span class="h1-suffix">(This Tag has {{ $tag->photos->count()==1 ? '1 Photo' : $tag->photos->count().' Photos' }} assigned.)</span>
						<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
						 		 data-toggle="collapse" data-target="#collapsei">
					 	</span></a>							
					</h1>
						<div id="collapsei" class="collapse {{ request()->has('pageI') ? 'show' : 'hide' }}" data-parent="#accordioni">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>Title</th>
									<th>Description</th>
									<th width="120px">Updated</th>
									<th width="130px" class="text-right">Page {{$photos->currentPage()}} of {{$photos->lastPage()}}</th>
								</thead>
								<tbody>						
									@foreach($photos as $photo)
										<tr>
											<th>{{ $photo->id }}</th>
											<td>{{ $photo->title }}</td>
											<td>
												{{ substr(strip_tags($photo->description), 0, 156) }}{{ strlen(strip_tags($photo->description)) >156 ? '...' : '' }}
											</td>
											<td>{{ date('j M Y', strtotime($photo->updated_at)) }}</td>
											<td class="text-right" nowrap>
												<a href="{{ route('photos.show', $photo->id)}}" class="btn btn-sm btn-outline-dark">View Photo</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<div class="d-flex justify-content-center">
								{{ $photos->appends(Request::all())->render() }} 
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif

	@endif
@endsection

@section('scripts')
@endsection
