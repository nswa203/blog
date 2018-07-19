@extends('main')

@section('title','| Contact')

@section('stylesheets')
	{!! Html::style('css/parsley.css') !!}
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron" style="padding-top:32px; padding-bottom:32px;">
				<h1><span class="fas fa-envelope mr-4"></span>Contact {{ $data['name'] }}</h1>
				<hr>
				{{ Form::open([url('contact'), 'method'=>'POST', 'data-parsley-validate'=>'']) }}
					<div class="row">
						<div class="col-md-6">
							{{ Form::label('name', 'Your Name:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('name', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191', 'autofocus'=>'',]) }}
						</div>
						<div class="col-md-6">
							{{ Form::label('email', 'Your eMail:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('email', null, ['class'=>'form-control form-control-lg', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'5', 'data-parsley-maxlength'=>'191']) }}
						</div>					
						<div class="col-md-12 mt-2">
							{{ Form::label('subject', 'Subject:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::text('subject', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'3', 'data-parsley-maxlength'=>'191']) }}

							{{ Form::label('message', 'Message:', ['class'=>'font-bold form-spacing-top']) }}
							{{ Form::textarea('message', null, ['class'=>'form-control', 'data-parsley-required'=>'', 'data-parsley-minlength'=>'8', 'data-parsley-maxlength'=>'2048', 'placeholder'=>'Type your message here...', 'id'=>'textarea-body', 'rows'=>'3']) }}

							{{ Form::label(null, 'Are You Human?', ['class'=>'font-bold form-spacing-top mr-5']) }}
							{{ Form::button('<h4><i class="far fa-envelope mr-3"></i>Send Your Message</h4>',
								['type'=>'submit', 'class'=>'form-control form-spacing-top btn btn-success float-lg-right float-none mb-3', 'style'=>'margin-top:54px; height:75px; width:300px;']) }}
							<div class="g-recaptcha" data-sitekey="{!! env('CAPTCHA_SITEKEY') !!}"></div>
						</div>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	{!! Html::script('js/parsley.min.js') !!}
	{!! Html::script('js/tinymce.min.js') !!}

	<script>
		tinymce.init ({
			selector: '#textarea-body',
			plugins: "link lists",
			menubar: false,
			toolbar: "",
			forced_root_block : 'div',
            branding: false,
		});
	</script>
@endsection
