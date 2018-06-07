@extends('manage')

@section('title','| Manage Edit User')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a id="menu-toggle2"><span class="fas fa-user-edit mr-4"></span>Edit User</a></h1>
			<hr>
			{!! Form::model($user, ['route'=>['users.update', $user->id], 'method'=>'PUT', 'data-parsley-validate'=>'']) !!}
			{{ 	Form::label('name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'v-model'=>'title', 'autofocus'=>'']) }}
	
			{{ 	Form::label('email', 'eMail:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('email', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'5', 'data-parsley-maxlength'=>'191', 'placeholder'=>"User's eMail address"]) }} 

			<div id="app3"> <!-- Vue 2 -->
				{{ Form::label('password', 'Password:', ['class'=>'font-bold form-spacing-top', 'v-if'=>'passwordOption == "manual"']) }}
				{{ Form::password('password', ['class'=>'form-control mt-2', 'id'=>'password', 'v-if'=>'passwordOption == "manual"', 'placeholder'=>'Manually provide a password for this User', 'v-focus'=>'']) }}
				<input type="hidden" name="itemsSelected" 	:value="itemsSelected">
				<input type="hidden" name="password_option" :value="passwordOption">
			</div> <!-- Vue 2 -->
		</div>

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row">
					<dt class="col-sm-5">URL:</dt>
					<dd class="col-sm-7"><a href="{{ route('users.show', $user->id) }}">{{ route('users.show', $user->id) }}</a></dd>
					<dt class="col-sm-5">User ID:</dt>
					<dd class="col-sm-7"><a href="{{ route('users.show', $user->id) }}">{{ $user->id }}</a></dd>							
					<dt class="col-sm-5">Created At:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->created_at)) }}</dd>
					<dt class="col-sm-5">Last Updated:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->updated_at)) }}</dd>
				</dl>
				<hr class="hr-spacing-top">

				<div id="app2" class="font-weight-bold"> <!-- Vue 2 -->
					<div class="field">
						<label for="password_options1" class="">
							{{ Form::radio('password_option', 'keep', null, 		['class'=>'', 'v-model'=>'passwordOption', 'hidden'=>'', 'id'=>'password_options1']) }}
							<span class="span"> Do Not Change Password</span>
						</label>
					</div>
					<div class="field">
						<label for="password_options2" class="mt-2">
							{{ Form::radio('password_option', 'auto', null, 		['class'=>'', 'v-model'=>'passwordOption', 'hidden'=>'', 'id'=>'password_options2']) }}
							<span class="span"> Auto-Generate New Password</span>
						</label>
					</div>
					<div class="field">
						<label for="password_options3" class="mt-2 mb-4">
							{{ Form::radio('password_option', 'manual', null, 		['class'=>'', 'v-model'=>'passwordOption', 'hidden'=>'', 'id'=>'password_options3']) }}
							<span class="span"> Manually Set New Password</span>
						</label>
					</div>
				</div> <!-- Vue 2 -->

				<hr class="hr-spacing-top">
				<div class="row">
					<div class="col-sm-6">
						{!! Html::decode('<a href='.url()->previous().' class="btn btn-danger btn-block"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
					</div>
					<div class="col-sm-6">
						{{ Form::button('<i class="fas fa-user-edit mr-2"></i>Save', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
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

	<div class="row mt-4">
		<div class="col-md-12">
			<div class="card card-body bg-light" id="app"> <!-- Vue 2 -->
				<h1>Roles<span class="h1-suffix">({{ $user->roles->count() }} Roles from {{ $roles->total() }}  have been assigned to this User.)</span></h1>
				<table class="table table-hover">
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
						<th width="120px">Updated At</th>
						<th width="130px" class="text-right">Page {{$roles->currentPage()}} of {{$roles->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($roles as $role)
							<tr>
								<th>{{ $role->id }}</th>
								<td>
									<label for="{!! $role->id !!}">
								    	<input hidden type="checkbox" id="{!! $role->id !!}" value="{!! $role->id !!}" v-model="itemsSelected" name=":custom-value" @change="checkAll('item')" />
										<span class="span"></span>
								    </label>
								</td>
								<td>{{ $role->display_name }}</td>
								<td>{{ $role->name }}</td>
								<td>{{ $role->description }}</td>
								<td>{{ date('j M Y', strtotime($role->updated_at)) }}</td>
								<td class="text-right">
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
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js')	!!}
	{!! Html::script('js/app.js') 			!!}

	<script>
		var commonData = {
			passwordOption: 'keep',
			itemsCheckAll: false,
			itemsAll: {!! $roles->pluck('id') !!},
			itemsSelected: {!! $user->roles->pluck('id') !!},
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
			data: commonData
		});

		var app=new Vue({
			el: '#app3',
			data: commonData
		});
	</script>	
@endsection
