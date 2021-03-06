<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="referrer" content="origin" />
<title>{{ config('app.name') }} @yield('title')</title>

<!-- My FavIcon ICO -->
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

<!-- Bootstrap CSS 
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous"> -->

<!-- Font Awesome CSS
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
Now Installed manually npm install @fortawesome/fontawesome-free" -->
<link type="text/css" rel="stylesheet" href="{{ mix('css/app.css') }}">

{{ Html::style('/css/styles.css') }}

<!-- Google Recaptcha JS -->
<script src='https://www.google.com/recaptcha/api.js'></script>
