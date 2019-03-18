@extends('main')

@section('stylesheets')
@endsection

@if ($folder)
	@section('title', "| $folder->slug")

	@section('content')
		<div class="row">
			<div class="col-md-12 myWrap">
				@if($folder->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$folder->banner) }}" width="100%"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</div>
				@endif
					<a href="{{ asset($folder->directory.'/Folder.jpg') }}">
						<img src="{{ asset($folder->directory.'/Folder.jpg') }}" height="150px" class="img-frame float-left mr-4" style="margin-top:-10px; margin-bottom:10px;"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</a>
				<h1>Folder: {{ $folder->name }}</h1>
				<p>{!! $folder->description !!}</p>
				<div style="clear:both;">
					<hr>
					<p>
						Posted In <a href="{{ url('blog?pca='.$folder->category->name) }}"><span class="badge badge-secondary">{{ $folder->category->name }}</span></a>
						<span class="float-right">Published: {{ date('j M Y, h:i a', strtotime($folder->published_at)) }}</span>
					</p>
				</div>
			</div>
		</div>
{{-- **************************************************************************** --}}
		@if($files)
			<div id="app"> <!-- Vue 2 -->
				{!! Form::open(['route'=>['files.mixed']]) !!}
							
				<div class="row">
					<div class="col-md-12 myWrap">
						<label for="itemsCheckAll" style="margin:8px 0px -10px 1px;">
					    	<input hidden type="checkbox" id="itemsCheckAll" @click="checkAll('all')" value="all" v-model="itemsCheckAll" name=":custom-value2" />
							<span class="span"></span>
					    </label>

						<h1 class="float-right">
							<a><span class="pointer-expand fas fa-chevron-circle-down mr-1"
							data-toggle="collapse" data-target=".collapsef" onClick="myView('file', 'accordionf2')">
						 	</span></a>
						</h1>
					</div>
				</div>

				<div class="row mt-1" id="accordionf">
					<div class="col-md-12 myWrap">
						<div id="accordionf1" class="collapse collapsef show" data-parent="#accordionf">
							<table class="table table-hover table-responsive-lg wrap-string">
								<thead class="thead-dark" style="color:inherit;">
									<th>{{ $folder->files->count() }}</th>
									<th class="thleft">
										<a href="{{ route('blog.folder', [$folder->slug, 'sort'=>'i'.$sort, 'search'=>$search]) }}">
											<i id="sort-i"></i><i class="fas fa-hashtag mb-1"></i>
										</a>		
									</th>
									<th class="thleft">
										<a href="{{ route('blog.folder', [$folder->slug, 'sort'=>'t'.$sort, 'search'=>$search]) }}">
											<i id="sort-t"></i>Title
										</a>	
									</th>
									<th class="thleft">Tags</th>
									<th class="thleft">
										<a href="{{ route('blog.folder', [$folder->slug, 'sort'=>'s'.$sort, 'search'=>$search]) }}">
											<i id="sort-s"></i>Size</th>
										</a>
									<th class="thleft" width="120px">
										<a href="{{ route('blog.folder', [$folder->slug, 'sort'=>'p'.$sort, 'search'=>$search]) }}">
											<i id="sort-p"></i>Published</th>
										</a>	
									<th width="110px" class="text-right">Page {{$files->currentPage()}} of {{$files->lastPage()}}</th>
								</thead>

								<tbody>
									@foreach($files as $file)
										<tr>
											<td class="tdleft">
												<label for="{!! $file->id !!}">
											    	<input hidden type="checkbox" id="{!! $file->id !!}" value="{!! $file->id !!}" v-model="itemsSelected" v-model="itemsAll" name=":custom-value" @change="checkAll('item')" />
													<span class="span"></span>
											    </label>
											</td>
											<th class="tdleft">{{ $file->id }}</th>
											<td class="tdleft">{{ myTrim($file->title, 32) }}</td>
											<td class="tdleft">
												@foreach ($file->tags as $tag)
													<a href="{{ route('blog.index', ['pta='.$tag->name]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
												@endforeach
											</td>
											<td class="tdleft">{{ mySize($file->size) }} </td>
											<th class="tdleft">
												@if($file->published_at)
													<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
														{{ date('j M Y', strtotime($file->published_at)) }}, {{ $list['d'][$file->folder->status] }}
													</span>
												@else	
													<span class="text-danger">{{ $list['f'][$file->status] }}, {{ $list['d'][$file->folder->status] }}</span>
												@endif	
											</th>
											<td>
												{{ Form::button('<i class="fas fa-search"   ></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'show,'.$file->id, 'class'=>'btn btn-sm btn-success float-right', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Show']) }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="accordionf2" class="collapse collapsef mb-2" data-parent="#accordionf">
					<div class="row ml-0 mr-0 mb-4">
						@foreach($files as $file)
							<div class="col-md-3 img-frame-lg" style="padding:10px 10px 15px 10px; background-color:gray;">
								<div class="mb-1">
									<label for="{!! $file->id !!}">
								    	<input hidden type="checkbox" id="{!! $file->id !!}" value="{!! $file->id !!}" v-model="itemsSelected" v-model="itemsAll" name=":custom-value" @change="checkAll('item')" />
										<span class="span" style="margin:0px -8px 0px 0px;"></span>
								    </label>
									{{ Form::button('<i class="fas fa-expand"></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'showFile,'.$file->id, 'class'=>'btn btn-sm btn-dark', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Zoom',
									'style'=>'margin:0px 0px 0px 0px; padding:2px 6px 3px 6px']) }}
								    <span class="small">{{ myTrim($file->title, 24) }}</span>
								</div>

								<div class="text-center">
									@if(substr($file->mime_type, 0, 2) == 'au' or substr($file->mime_type, 0, 2) == 'vi' or $file->ext == 'mp3')
										<a href="{{ route('files.showFile', [$file->id]) }}">
											<video controls poster="{{
													isset(json_decode($file->meta)->Picture) ? 'data:image/jpeg;base64,' . json_decode($file->meta)->Picture :
													(substr($file->mime_type, 0, 2) == 'au' ? asset('favicon.ico') : '') }}"
												class="img-frame-lg" onLoadedData="myVolume('session')">
							    				<source src="{{ route('private.getFile', [$file->id]) }}" type="{{ $file->mime_type }}" />
							    				<source src="{{ route('private.getFile', [$file->id]) }}" type="video/mp4" />
											</video>
										</a>
									@elseif(substr($file->mime_type, 0, 2) == 'im')
										{{-- https://blog/manage/private/id?r=n&t=250 --}}
										<a href="{{ route('files.showFile', [$file->id]) }}">
											<img src="{{ route('private.getFile', [$file->id, 'r=n&t=250']) }}"
											class="img-frame-lg"
											style="max-height:200px; max-width:100%;"
											xonerror="this.onerror=null; myImgDebug(this);" 
											onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" /> 
										</a>
									@else
										{{-- Here we provide icons for different filetype extensions                                  --}}
										{{-- They should be pre-loaded into the db in $folder->name=icons $file->title=file_extension --}}
									    {{-- Debug: https://blog/manage/private/find/pdf/icons 										  --}}
										<a href="{{ route('private.getFile', [$file->id]) }}">
											<img src="{{ route('private.findFile',
											[pathinfo($file->file, PATHINFO_EXTENSION), 'icons']) }}" {{-- searches title not .ext --}}
											class="img-frame-lg"
											style="max-height:200px; max-width:100%;"
											onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" />
										</a>
									@endif
								</div>
							</div>
						@endforeach
					</div>
				</div>

				<div>
					{{ Form::text('itemsSelected', null, [':value'=>'itemsSelected', 'hidden'=>'']) }}
				</div>
				{!! Form::close() !!}
			</div> <!-- Vue 2 app -->

			<div class="d-flex justify-content-center">
				{{ $files->appends(Request::only(['search', 'sort']))->render() }} 
			</div>
		@endif
{{-- **************************************************************************** --}}
	@endsection
@endif

@section('scripts')
	{!! Html::script('js/app.js')     !!}
	{!! Html::script('js/helpers.js') !!} 	 

	<script>
		mySortArrow({!! json_encode($sort) !!});
		myView('file', 'accordionf2', 'accordionf1');
	</script>

	<script>
		var commonData = {
			itemsCheckAll: false,
			itemsAll: {!! $files->pluck('id') !!},
			itemsSelected: [{!! Request::old('itemsSelected') !!}],
		};
		
		var app=new Vue({
			el: '#app',
			data: commonData,
			methods: {
			    checkAll: function(op='item') {
			    	if (op=='all'){
			    		if (itemsCheckAll.checked) {
			    			this.itemsSelected=this.itemsAll;
						} else {
		    				this.itemsSelected=[];
		    			}	
			    	} else {
		    			this.$nextTick(() => { itemsCheckAll.checked=false; });
			    	}
			   }
		    },
		});
	</script>
@endsection
