@extends('manage')

@section('title','| Manage Create Role')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a id="menu-toggle2"><span class="fas fa-user-plus mr-4"></span>Create A New Role</a></h1>
			<hr>
			{!! Form::open(['route'=>'roles.store', 'data-parsley-validate'=>'']) !!}
		
			{{ 	Form::label('display_name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('display_name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'autofocus'=>'']) }}
	
			{{ 	Form::label('name', 'Slug:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('name', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'96']) }} 

			{{ 	Form::label('description', 'Description:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('description', null, ['class'=>'form-control', 'data-parsley-maxlength'=>'191']) }}

			<div id="app2"> <!-- Vue 2 -->
				<input type="hidden" name="permissions" :value="itemsSelected">
			</div> <!-- Vue 2 -->
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row">
					<dt class="col-sm-5">Created At:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>
				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-user-plus mr-2"></i>Create Role', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('roles.index', '<i class="fas fa-user-friends mr-2"></i>See All Roles', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>
		</div>
	</div>
 
	<div class="row mt-5">
		<div class="col-md-12">
			<div class="card card-body bg-light" id="app"> <!-- Vue 2 -->
				<h1>Permissions<span class="h1-suffix">({{ $permissions->total() }} may be assigned to this Role.)</span></h1>
				<table class="table table-hover">
					<thead class="thead-dark">
						<th>#</th>
						<th width="10px">
							<label for="itemsCheckAll" >
						    	<input hidden type="checkbox" id="itemsCheckAll" @click="checkAll('all')" value="all" v-model="itemsCheckAll" name=":custom-value2" />
								<span class="span"></span>
						    </label>
						</th>
						<th>Name</th>
						<th>Slug</th>
						<th>Description</th>
						<th width="120px">Updated At</th>
						<th width="120px">Page {{$permissions->currentPage()}} of {{$permissions->lastPage()}}</th>
					</thead>
					<tbody>	
						@foreach($permissions as $permission)
							<tr>
								<th>{{ $permission->id }}</th>
								<td>
									<label for="{!! $permission->id !!}">
								    	<input hidden type="checkbox" id="{!! $permission->id !!}" value="{!! $permission->id !!}" v-model="itemsSelected" v-model="itemsAll" name=":custom-value" @change="checkAll('item')" />
										<span class="span"></span>
								    </label>
								</td>
								<td>{{ $permission->display_name }}</td>
								<td>{{ $permission->name }}</td>
								<td>{{ $permission->description }}</td>
								<td>{{ date('j M Y', strtotime($permission->updated_at)) }}</td>
								<td>
									<a href="{{ route('permissions.show', $permission->id)}}" class="btn btn-sm btn-outline-dark">View Permission</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>

				<div class="d-flex justify-content-center">
					{!! $permissions->render() !!} 
				</div>
			</div> <!-- Vue 2 -->
		</div>
		{!! Form::close() !!}
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js')	!!}
	{!! Html::script('js/app.js') 			!!}

	<script>
	var commonData = {
		itemsAll: {!! $permissions->pluck('id') !!},
		itemsSelected: [],
		itemsCheckAll: false,
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
