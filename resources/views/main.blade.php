<!doctype html>
<html lang="en">
	<head>
		@include('partials._head')
		@yield('stylesheets')
	</head>
	
	<body>
        @include('partials._nav_main')

        <div id="wrapper">
            {{-- @include('partials._nav_manage') --}}
            
            <div id="page-content-wrapper">
                <div class="container-fluid">
   					<div class="col-md-0 col-xs-12 col-sm-12">
						<div class="col-md-8 offset-md-2 mt-4">
		                    @include('partials._messages')
        	            	@yield('content')
            			</div>
            		</div>
                </div>
            </div>
        </div>
        @include('partials._footer')

        @include('partials._javascript')
        @yield('scripts')

        <!-- Menu Toggle Script -->
        <script>
            //$("#menu-toggle").click(function(e) {
            //    e.preventDefault();
            //    $("#wrapper").toggleClass("toggled");
            //});
            //$("#menu-toggle2").click(function(e) {
            //    e.preventDefault();
            //    $("#wrapper").toggleClass("toggled");
            //});
        </script>

    </body>
</html>
