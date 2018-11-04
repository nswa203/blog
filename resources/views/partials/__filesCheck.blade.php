{{-- Included in 	files.copy
					files.delete
					files.move
--}}

<div class="row mt-3">
	<div class="col-md-12 myWrap" > 
		<table class="table table-hover table-responsive-lg wrap-string" id="app"> <!-- Vue 2 -->
			<thead class="thead-dark">
				<th width="20px">
					<i class="fas fa-hashtag mb-1 ml-2"></i>
				</th>
				<th width="10px">
					<label for="itemsCheckAll" >
				    	<input hidden type="checkbox" id="itemsCheckAll" @click="checkAll('all')" value="all" v-model="itemsCheckAll" name=":custom-value2" />
						<span class="span"></span>
				    </label>
				</th>
				<th>Title</th>
				<th>Folder</th>
				<th>Tags</th>
				<th>Owner</th>
				<th>Size</th>
				<th width="180px">Published</th>
			</thead>
			<tbody>
				@foreach($files as $file)
					<tr>
						<th>{{ $file->id }}</th>
						<td>
							<label for="{!! $file->id !!}">
						    	<input hidden type="checkbox" id="{!! $file->id !!}" value="{!! $file->id !!}" v-model="itemsSelected" v-model="itemsAll" name=":custom-value" @change="checkAll('item')" />
								<span class="span"></span>
						    </label>
						</td>
						<td>{{ myTrim($file->title, 32) }}</td>
						<td>
							<a href="{{ route('folders.show', [$file->folder->id, session('zone')]) }}">
								<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
									{{ myTrim($file->folder->name, 32) }}
								</span>
							</a>
					    </td>
						<td>
							@foreach ($file->tags as $tag)
								<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
							@endforeach
						</td>
						<td>
							<a href="{{ route('users.show', $file->folder->user_id) }}">{{ $file->folder->user->name }}</a>
						</td>
						<td>{{ mySize($file->size) }} </td>
						<th>
							@if($file->published_at)
								<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
									{{ date('j M Y', strtotime($file->published_at)) }}, {{ $list['d'][$file->folder->status] }}
								</span>
							@else	
								<span class="text-danger">{{ $list['f'][$file->status] }}, {{ $list['d'][$file->folder->status] }}</span>
							@endif	
						</th>
					</tr>
				@endforeach
			</tbody>
		</table>
		<div id="app2"> <!-- Vue 2 -->
			<input type="hidden" name="itemsSelected" :value="itemsSelected">
		</div> <!-- Vue 2 -->
	</div>
</div>

@section('scripts')
	{!! Html::script('js/app.js') 		  !!}
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/select2.min.js') !!}

	<script type="text/javascript">
		$.fn.select2.defaults.set("width", "100%");
		// Above line must be first to ensure Select2 works
		// nicely alongside Bootrap 4
		$('.select2-single').select2({
			placeholder: "Change status...",
			maximumSelectionLength: 1
		});   
		$('.select2-multi').select2({
			placeholder: "Change tags..."
		});
	</script>

	<script>
		var commonData = {
			itemsCheckAll: true,
			itemsAll: {!! $files->pluck('id') !!},
			itemsSelected: [{!! $itemsSelected !!}],
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
		
		var app=new Vue({
			el: '#app2',
			data: commonData,			
			});	
	</script>
@endsection
