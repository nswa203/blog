<h3>{{ env('APP_NAME') }}</h3>
<h3>You have a New Contact via The Contact Form</h3>

<div class="">
	{{ $bodyMessage }}
</div>

<p>Sent from {{ $email }}</p>
