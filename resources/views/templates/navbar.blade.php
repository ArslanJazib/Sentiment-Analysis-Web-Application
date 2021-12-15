@section('navbar')
<!-- Nav bar-->
<div class="container-fluid" id="navCont">
    <div class="custom-menu">
        <button type="button" id="sidebarCollapse" class="btn btn-primary">
            <i class="fa fa-bars"></i>
        </button>
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-md-7"></div>
        <!-- Hyper Links -->
        <div class="col-md-5">
            <!-- NAVIGATION -->
            <nav>
                <ul class="main-nav nav">
                    <li class="active">
                        <a href="#">
                            <i class="fa fa-table fa-fw" style="color: darkred"></i> Listing
                        </a></li>
                    <li>
                        <a href="#">
                            <i class="fa fa-wpforms fa-fw" style="color: darkred"></i> Form
                        </a></li>
                    <li>
                        <a href="#">
                            <i class="fa fa-sign-out fa-fw" style="color: darkred"></i> Sign Out
                        </a></li>
                </ul>
                <!-- /NAV -->
            </nav>
            <!-- /NAVIGATION -->
        </div>
    </div>
    <!-- row -->
</div>
<!-- /Nav bar-->
@endsection
