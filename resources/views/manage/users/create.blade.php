@extends('manage')

@section('title','| Manage Create User')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user-plus mr-4"></span>Create New User</a></h1>
			<hr>
			{!! Form::open(['route'=>'users.store', 'data-parsley-validate'=>'']) !!}
			{{ 	Form::label('name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'v-model'=>'title', 'autofocus'=>'']) }}
	
			{{ 	Form::label('email', 'eMail:', ['class'=>'font-bold form-spacing-top mr-3']) }}
			<span class="text-danger fas fa-envelope mr-2"></span><span class="font-weight-bold text-danger">A notification eMail will be sent to the user.</span>
			{{ 	Form::text('email', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'5', 'data-parsley-maxlength'=>'191', 'placeholder'=>"User's eMail address"]) }} 

			<div id="app"> <!-- Vue 2 -->
				{{ Form::label('password', 'Password:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::password('password', ['class'=>'form-control', 'id'=>'password', ':disabled'=>'auto_password', 'placeholder'=>'Manually provide a password for this User', 'v-focus'=>'']) }}

				{{ Form::label('auto_generate', 'Auto Generate Password:', ['class'=>'font-bold form-spacing-top mr-2']) }}
				<label for="auto_generate">
					{{ Form::checkbox('auto_generate', '1', null, ['class'=>'', 'v-model'=>'auto_password', 'id'=>'auto_generate', 'hidden'=>'']) }}
					<span class="span"></span>
				</label>
				<input type="hidden" name="itemsSelected" :value="itemsSelected">
			</div> <!-- Vue 2 -->
		</div>
		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>

				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href="Return" class="btn btn-danger btn-block" onclick="window.history.back()"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-user-plus mr-2"></i>Create', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('users.index', '<i class="fas fa-user-friends mr-2"></i>See All Users', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-md-12">
			<div class="card card-body bg-light" id="app2"> <!-- Vue 2 -->
				<h1>Roles<span class="h1-suffix">({{ $roles->total() }} Roles may be assigned to this User.)</span></h1>
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark">
						<th><span class="fas fa-hashtag mb-2 ml-1"></th>
						<th width="10px">
							<label for="itemsCheckAll">
						    	<input hidden type="checkbox" id="itemsCheckAll" @click="checkAll('all')" value="all" v-model="itemsCheckAll" name=":custom-value2" />
								<span class="span"></span>
						    </label>
						</th>
						<th>Name</th>
						<th>Slug</th>
						<th>Description</th>
						<th width="120">Updated</th>
						<th width="130" class="text-right">Page {{$roles->currentPage()}} of {{$roles->lastPage()}}</th>
					</thead>
					<tbody>	
						@foreach($roles as $role)
							<tr>
								<th>{{ $role->id }}</th>
								<td>
									<label for="{!! $role->id !!}">
								    	<input hidden type="checkbox" id="{!! $role->id !!}" value="{!! $role->id !!}" v-model="itemsSelected" v-model="itemsAll" name=":custom-value" @change="checkAll('item')" />
										<span class="span"></span>
								    </label>
								</td>
								<td>{{ $role->display_name }}</td>
								<td>{{ $role->name }}</td>
								<td>{{ substr($role->description, 0, 156) }}{{ strlen($role->description)>156 ? '...' : '' }}</td>
								<td>{{ date('jM ', strtotime($role->updated_at)) }}</td>
								<td class="text-right" nowrap>
									<a href="{{ route('roles.show', $role->id)}}" class="btn btn-sm btn-outline-dark">View Role</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>

				<div class="d-flex justify-content-center">
					{!! $roles->render() !!} 
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
			auto_password: true,
			itemsCheckAll: false,
			itemsAll: {!! $roles->pluck('id') !!},
			itemsSelected: [{!! Request::old('itemsSelected') !!}],
		};

		Vue.directive('focus', {
		    inserted: function (el) {
		        Vue.nextTick(function() {
			        el.focus();
		    	});
		    },	
		    update: function (el) {
		        Vue.nextTick(function() {
		            el.focus();
		        })
		    }
		})

		var app=new Vue({
			el: '#app',
			data: commonData,
		});

		var app=new Vue({
			el: '#app2',
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
