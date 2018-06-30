<!doctype html>
<html lang="en">
	<head>
		@include('partials._headbare')
		@yield('stylesheets')
	</head>
	<body>
       	@yield('content')
        @include('partials._javascript')
    </body>
</html>
