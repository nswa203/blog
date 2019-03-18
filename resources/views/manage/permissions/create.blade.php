@extends('manage')

@section('title','| Manage Create Permission')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8 myWrap">
			<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user-plus mr-4"></span>Create New Permission</a></h1>
			<hr>
			{!! Form::open(['route'=>'permissions.store', 'data-parsley-validate'=>'', 'files'=>true]) !!}

			<div id="app"> <!-- Vue 2 -->
				<input hidden name="permission_type" :value="permissionType">
				<div class="field" v-if="permissionType == 'basic'">
					{{ Form::label('display_name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
					{{ Form::text('display_name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'v-focus'=>'']) }}

					{{ Form::label('name', 'Slug:', ['class'=>'font-bold form-spacing-top']) }}
					{{ Form::text('name', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191' ]) }} 

					{{ Form::label('description', 'Description:',['class'=>'font-bold form-spacing-top']) }}
					{{ Form::text('description', null, ['class'=>'form-control', 'data-parsley-maxlength'=>'191']) }} 
				</div>

				<div class="field" v-if="permissionType == 'crud'">
					{{ Form::label('resource', 'Resource:', ['class'=>'font-bold form-spacing-top']) }}
					{{ Form::text('resource', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'v-model'=>'resource', 'v-focus'=>'']) }}
				</div>

				<div class="field mt-5" v-if="permissionType == 'crud'">
					<input hidden name="crud_selected" :value="crudSelected">
					<div class="column">
						<table class="table">
							<thead class="thead-dark">
								<th>Name</th>
								<th>Slug</th>
								<th>Description</th>
							</thead>
							<tbody v-if="resource.length>=3">
								<tr v-for="item in crudSelected">
									<td v-text="crudName(item)"></td>
									<td v-text="crudSlug(item)"></td>
									<td v-text="crudDescription(item)"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div> <!-- Vue 2 -->
		</div>	

		<div class="col-md-4">
			<div class="card card-body bg-light">
				<dl class="row dd-nowrap">
					<dt class="col-sm-5">Created:</dt>
					<dd class="col-sm-7">{{ date('j M Y, h:i a') }}</dd>
				</dl>
				<hr class="hr-spacing-top">
				<div id="app2"> <!-- Vue 2 -->
					<dt>
						<div class="field">
							<label for="permission_type1" class="">
								{{ Form::radio('permission_type', 'basic', null, ['class'=>'', 'v-model'=>'permissionType', 'hidden'=>'', 'id'=>'permission_type1']) }}
								<span class="span"> Basic Permission</span>
							</label>
						</div>
					</dt>
					<dt>	
						<div class="field">
							<label for="permission_type2" class="mt-2">
								{{ Form::radio('permission_type', 'crud', null, ['class'=>'', 'v-model'=>'permissionType', 'hidden'=>'', 'id'=>'permission_type2']) }}
								<span class="span"> C.R.U.D. Permissions</span>
							</label>
						</div>
					</dt>
					<hr class="hr-spacing-top mt-2">

					<div v-if="permissionType == 'crud'">
						<div class="row">
							<div class="col-md-6">
								<dt>
									<label for="crud_selected1">
										{{ Form::checkbox('custom-value', 'create', null,['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected1']) }}
										<span class="span"> (C) Create</span>
									</label>
								</dt>
								<dt>		
									<label for="crud_selected2">
										{{ Form::checkbox('custom-value', 'read', null, ['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected2']) }}
										<span class="span"> (R) Read</span>
									</label>
								</dt>
								<dt>
									<label for="crud_selected3">
										{{ Form::checkbox('custom-value', 'update', null,['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected3']) }}
										<span class="span"> (U) Update</span>
									</label>
								</dt>
								<dt>		
									<label for="crud_selected4">
										{{ Form::checkbox('custom-value', 'delete', null, ['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected4']) }}
										<span class="span"> (D) Delete</span>
									</label>
								</dt>
							</div>
							<div class="col-md-6">
								<dt>		
									<label for="crud_selected5">
										{{ Form::checkbox('custom-value', 'create-ifowner', null, ['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected5', '@click'=>'uncheckOther($event, "create")']) }}
										<span class="span"> If Owner</span>
									</label>
								</dt>
								<dt>		
									<label for="crud_selected6">
										{{ Form::checkbox('custom-value', 'read-ifowner', null, ['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected6', '@click'=>'uncheckOther($event, "read")']) }}
										<span class="span"> If Owner</span>
									</label>
								</dt>
								<dt>		
									<label for="crud_selected7">
										{{ Form::checkbox('custom-value', 'update-ifowner', null, ['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected7', '@click'=>'uncheckOther($event, "update")']) }}
										<span class="span"> If Owner</span>
									</label>
								</dt>
								<dt>		
									<label for="crud_selected8">
										{{ Form::checkbox('custom-value', 'delete-ifowner', null, ['class'=>'', 'v-model'=>'crudSelected', 'hidden'=>'', 'id'=>'crud_selected8', '@click'=>'uncheckOther($event, "delete")']) }}
										<span class="span"> If Owner</span>
									</label>
								</dt>
							</div>
						</div>

						<hr class="hr-spacing-top mt-2">
					</div>

				</div> <!-- Vue 2 -->	

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
						{!! Html::decode(link_to_route('permissions.index', '<i class="fas fa-user-friends mr-2"></i>See All Permissions', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
					</div>
				</div>
			</div>
		{!! Form::close() !!}
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js')	!!}
	{!! Html::script('js/app.js') 			!!}

	<script>
		var commonData = {
			permissionType: 'basic',
			resource: '',
			crudSelected: ['create', 'read', 'update', 'delete'],
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
				crudName: function(item) {
					return app.resource.substr(0,1).toUpperCase()+app.resource.substr(1)+" "+item.substr(0,1).toUpperCase()+item.substr(1);	
				},
				crudSlug: function(item) {
					return app.resource.toLowerCase()+"-"+item.toLowerCase();
				},
				crudDescription: function(item) {
					return "Permits a User to "+item.toUpperCase()+" resource "+app.resource.substr(0,1).toUpperCase()+app.resource.substr(1);	
				}
			}
		});

		var app2=new Vue({
			el: '#app2',
			data: commonData,
			methods:{
				uncheckOther: function(e, item) {
					return true; // Not Using this code here so just return
					if (e.target.checked) {
						for (var i=0; i<this.crudSelected.length; ++i) { 
							if (this.crudSelected[i]==item) {
								this.crudSelected.splice(i, 1);
							}
						}
					}
				}
			}
		});
	</script>	
@endsection
