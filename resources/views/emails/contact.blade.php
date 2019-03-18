<h3>{{ config('app.name') }}</h3>
<h3>You have a New Contact via The Contact Form</h3>

<div class="">
	{{ $bodyMessage }}
</div>

<p>Sent from {{ $name }}</p>
<p>eMail: {{ $email }}</p>
