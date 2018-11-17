@extends('manage')

@section('title','| Manage Files')

@section('stylesheets')
@endsection

@section('content')
	@if($files)
		<div id="app"> <!-- Vue 2 -->
			{!! Form::open(['route'=>'files.mixed']) !!}
			<div class="row">
				<div class="col-md-9 myWrap">
					<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
						@if (isset($search)) <span class="fas fa-search mr-4"></span>
						@else 				 <span class="fas fa-folder-open mr-4"></span>
						@endif 				 Manage Files 
					</a></h1>
				</div>

				<div class="col-md-3">
					<a href="{{ route('files.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Add Files</a>
				</div>
				<div class="col-md-12">
					<hr>
				</div>
			</div>
			
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
								<th></th>
								<th class="thleft">
									<a href="{{ route('files.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
										<i id="sort-i"></i><i class="fas fa-hashtag mb-1"></i>
									</a>		
								</th>
								<th class="thleft">
									<a href="{{ route('files.index', ['sort'=>'t'.$sort, 'search'=>$search]) }}">
										<i id="sort-t"></i>Title
									</a>	
								</th>
								<th class="thleft">Folder</th>
								<th class="thleft">Tags</th>
								<th class="thleft">Owner</th>
								<th class="thleft">
									<a href="{{ route('files.index', ['sort'=>'s'.$sort, 'search'=>$search]) }}">
										<i id="sort-s"></i>Size</th>
									</a>
								<th class="thleft" width="120px">
									<a href="{{ route('files.index', ['sort'=>'p'.$sort, 'search'=>$search]) }}">
										<i id="sort-p"></i>Published</th>
									</a>	
								<th width="180px" class="text-right">Page {{$files->currentPage()}} of {{$files->lastPage()}}</th>
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
											<a href="{{ route('folders.show', [$file->folder->id, session('zone')]) }}">
												<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
													{{ myTrim($file->folder->name, 32) }}
												</span>
											</a>
									    </td>
										<td class="tdleft">
											@foreach ($file->tags as $tag)
												<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
											@endforeach
										</td>
										<td class="tdleft">
											<a href="{{ route('users.show', $file->folder->user_id) }}">{{ $file->folder->user->name }}</a>
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
										<td class="tdright">
											{{ Form::button('<i class="fas fa-search"   ></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'show,'.$file->id, 'class'=>'btn btn-sm btn-success', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Show']) }}
											
											{{ Form::button('<i class="far fa-edit"     ></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'edit,'.$file->id, 'class'=>'btn btn-sm btn-primary', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Edit']) }}

											{{ Form::button('<i class="fas fa-forward"    ></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'copy,'.$file->id, 'class'=>'btn btn-sm btn-primary', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Copy']) }}
											
											{{ Form::button('<i class="fas fa-share"      ></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'move,'.$file->id, 'class'=>'btn btn-sm btn-primary', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Move']) }}

											{{ Form::button('<i class="far fa-trash-alt"  ></i>', ['type'=>'submit', 'name'=>'choice', 'value' => 'delete,'.$file->id, 'class'=>'btn btn-sm btn-danger', 'data-toggle'=>'tooltip', 'data-placement'=>'bottom', 'title'=>'Delete']) }}
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
									<a href="{{ route('files.showFile', [$file->id]) }}">
										<img src="{{ route('private.getFile', [$file->id, 'r=n']) }}"
										class="img-frame-lg"
										style="max-height:200px; max-width:100%;"
										xonerror="this.onerror=null; myImgDebug(this);" 
										onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" /> 
									</a>
								@else
									<a href="{{ route('private.getFile', [$file->id]) }}">
										<img src="{{ route('private.findFile', [$file->ext, 'icons']) }}" {{-- searches title no .ext --}}
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
@endsection

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
