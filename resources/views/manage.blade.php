<!DOCTYPE html>
<!doctype html>
<html lang="en">
    <head>
        @include('partials._head')
        @yield('stylesheets')
    </head>
    
    <body>
        @include('partials._nav_main')
        <div id="wrapper">
            @include('partials._nav_manage')

            <div id="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-10 offset-md-1 mt-4">
                            @include('partials._messages')

                            @yield('content')
                        </div>
                    </div>
                </div>

            @include('partials._footer')
            </div>
        </div>

        @include('partials._javascript')
        @yield('scripts')

        <!-- Responsive Menu Toggle Script designd to hide automatically -->
        <!-- at same small screen size as top Nav bar.                   -->
        <!-- 992=Top NavBar 1206=Blog Tables                             -->
        <script>
            function sidebarResize() {
                if ($(window).width() >= 1206) {
                    $("#wrapper").addClass("toggled");
                } else {
                    $("#wrapper").removeClass("toggled");
                }
            }

            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });

            $("#menu-toggle2").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });
            document.getElementById('menu-toggle').click();

           $(window).resize(sidebarResize);
            sidebarResize();
        </script>
    </body>
</html>
