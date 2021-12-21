@extends('layout.masterLayout')
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- As a heading -->
    <nav class="navbar" style="background-color: black">
        <div class="container-fluid">
            <p style="font-family:'SelfDeceptionRegular';font-size: xx-large;margin-top: 0;margin-bottom: -1rem">SentiEntrepreneur</p>
            <span class="navbar-brand">Search Page</span>
        </div>
    </nav>
    <div class="s013">
        <form id="searchForm">
            @csrf
            <div class="inner-form">
                <div class="left">
                    <div class="input-wrap first">
                        <div class="input-field first">
                            <label><i class="fas fa-search fa-fw" style="color: white"></i>Topic</label>
                            <input id="search_bar" name="searchRequest" class="form-control form-control-lg" type="text"
                                   placeholder="E.g: Artificial Intelligence, Machine Learning, Python"
                                   aria-label=".form-control-lg example">

                        </div>
                    </div>
                    <div class="input-wrap second">
                        <div class="input-field second">
                            <label>Mode</label>
                            <div class="input-select">
                                <select id="mode_dropdown" data-trigger="" name="modeChoice">
                                    <option>General</option>
                                    <option>Start-Up</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="search_btn" class="btn-search" type="submit">Analyze Sentiment</button>
            </div>
        </form>
    </div>
    <footer class="footer">
        <img style="margin: 0.5%" class="box small img-fluid" src="{{asset('assets/images/twitter.png')}}" alt="...">
    </footer>
    <script>
        $(document).ready(function () {
            $("#pageBody").css("background-image", "url('assets/images/mainPage_background.png')");
        });

        $('#searchForm').submit(function (e) {
            e.preventDefault();
            var search_val = $('#search_bar').val();
            var mode_val = $('#mode_dropdown').val();

            $("#pageBody").busyLoad("show", {
                background: "rgba(255,255,255,0.5)",
                image: "{{asset('assets/images/loading.gif')}}",
                maxSize: "500px",
            });
            $.ajax({
                type: 'GET',
                url: "{{url('/submitRequest')}}",
                data: {searchRequest: search_val, modeChoice: mode_val},
                success: function () {
                    window.location.href = '{{url('/visualize')}}' + "?searchRequest=" + search_val+"&modeChoice="+mode_val;
                },
                complete: function () {
                    $("#pageBody").busyLoad("hide");
                }
            });
        });
    </script>
@endsection
