@extends('manage')

@section('title','| Manage Create User')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-8">
			<h1><a id="menu-toggle2"><span class="fas fa-user-plus mr-4"></span>Create New User</a></h1>
			<hr>
			{!! Form::open(['route'=>'users.store', 'data-parsley-validate'=>'']) !!}
			{{ 	Form::label('name', 'Name:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'v-model'=>'title', 'autofocus'=>'']) }}
	
			{{ 	Form::label('email', 'eMail:', ['class'=>'font-bold form-spacing-top']) }}
			{{ 	Form::text('email', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'5', 'data-parsley-maxlength'=>'191', 'placeholder'=>"User's eMail address"]) }} 

			<div id="app"> <!-- Vue 2 -->
				{{ Form::label('password', 'Password:', ['class'=>'font-bold form-spacing-top']) }}
				{{ Form::password('password', ['class'=>'form-control', 'id'=>'password', ':disabled'=>'auto_password', 'placeholder'=>'Manually provide a password for this User', 'v-focus'=>'']) }}

				{{ Form::label('auto_generate', 'Auto Generate Password:', ['class'=>'font-bold form-spacing-top mr-2']) }}
				<label for="auto_generate">
					{{ Form::checkbox('auto_generate', '1', null, ['class'=>'', 'v-model'=>'auto_password', 'id'=>'auto_generate', 'hidden'=>'']) }}
					<span class="span"></span>
				</label>
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
						{{ Form::button('<i class="fas fa-user-plus mr-2"></i>Create User', ['type'=>'submit', 'class'=>'btn btn-success btn-block']) }}
					</div>
				</div>
				<div class="row mt-3">
					<div class="col-sm-12">
						{!! Html::decode(link_to_route('users.index', '<i class="fas fa-user-friends mr-2"></i>See All Users', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
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
			data: {
				auto_password: true
			}
		});
	</script>	
@endsection
