@extends('manage')

@section('title','| Manage Edit Role')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a id="menu-toggle2"><span class="fas fa-user-edit mr-4"></span>Edit Role</a></h1>
			<hr>
			{!! Form::model($role, ['route'=>['roles.update', $role->id], 'method'=>'PUT', 'data-parsley-validate'=>'']) !!}
			
			{{ 	Form::label('display_name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('display_name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'autofocus'=>'']) }}
	
			{{ 	Form::label('name', 'Slug:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('name', null, ['class'=>'form-control', 'disabled'=>'']) }} 

			{{ 	Form::label('description', 'Description:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('description', null, ['class'=>'form-control', 'data-parsley-maxlength'=>'191']) }}

			<div id="app2"> <!-- Vue 2 -->
				<input type="hidden" name="itemsSelected" :value="itemsSelected">
			</div> <!-- Vue 2 -->
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row">
					<dt class="col-sm-5">URL:</dt>
					<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ route('roles.show', $role->id) }}</a></dd>
					<dt class="col-sm-5">Role ID:</dt>
					<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ $role->id }}</a></dd>							
					<dt class="col-sm-5">Created At:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->created_at)) }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->updated_at)) }}</dd>
				</dl>
				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-user-edit mr-2"></i>Save Changes', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
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
				<h1>Permissions<span class="h1-suffix">({{ $role->permissions->count() }} Permissions from {{ $permissions->total() }} have been assigned to this Role.)</span></h1>
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
						<th>Updated At</th>
						<th class="text-right">Page {{$permissions->currentPage()}} of {{$permissions->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($permissions as $permission)
							<tr>
								<th>{{ $permission->id }}</th>
								<td>
									<label for="{!! $permission->id !!}">
								    	<input hidden type="checkbox" id="{!! $permission->id !!}" value="{!! $permission->id !!}" v-model="itemsSelected" name=":custom-value" @change="checkAll('item')" />
										<span class="span"></span>
								    </label>
								</td>
								<td>{{ $permission->display_name }}</td>
								<td>{{ $permission->name }}</td>
								<td>{{ $permission->description }}</td>
								<td>{{ date('j M Y', strtotime($permission->updated_at)) }}</td>
								<td class="text-right">
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
		itemsCheckAll: false,
		itemsAll: {!! $permissions->pluck('id') !!},
		itemsSelected: {!! $role->permissions->pluck('id') !!},
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
