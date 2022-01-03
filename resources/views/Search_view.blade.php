@extends('layout.masterLayout')
@section('content')
    <!-- As a heading -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-3">
                    <img style="margin: 0.5%" class="box small img-fluid" src="{{asset('assets/images/FYPLogo.png')}}"
                         alt="...">
                    <p style="display:inline;font-family:'SelfDeceptionRegular';font-size: xx-large;margin-top: 0;margin-bottom: -1rem">
                        SentiEntrepreneur</p>
                </div>
                <div style="text-align: end;" class="col-9">
                    <button id="cancel-button" type="button" class="btn btn-danger btn-lg ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                    <span class="navbar-brand">Search Page</span>
                </div>
            </div>
        </div>
    </nav>
    <div id="div_for_cancel_button" style="height: 80vh" class="wrapper d-flex align-items-stretch">
        <nav id="sidebar">
            <div class="custom-menu">
                <button type="button" id="sidebarCollapse" class="btn btn-primary">
                    <i style="color: black" class="fas fa-lightbulb"></i>
                    <span class="sr-only">Toggle Menu</span>
                </button>
            </div>
            <div class="p-4 pt-5">
                <h6><a style="color: white" href="#" class="logo">Topic Recommendations</a></h6>

                <ul id="recommendation_list" class="list-unstyled components mb-5">

                </ul>
            </div>
        </nav>
        <div class="s013">
            <div class="alert alert-danger print-error-msg" style="display:none">
                <ul></ul>
            </div>
            <form id="searchForm">
                @csrf
                <div class="inner-form">
                    <div class="left">
                        <div class="input-wrap first">
                            <div class="input-field first">
                                <label><i class="fas fa-search fa-fw" style="color: white"></i>Topic</label>
                                <input id="search_bar" name="searchRequest" class="form-control form-control-lg"
                                       type="text"
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
    </div>
    <footer class="footer-14398">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <img style="margin: 0.5%" class="box small img-fluid" src="{{asset('assets/images/FYPLogo.png')}}"
                         alt="...">
                    <p style=" display:inline;font-family:'SelfDeceptionRegular';font-size: xx-large;margin-top: 0;margin-bottom: -1rem">
                        SentiEntrepreneur
                    </p>
                    <p style="color: #777">See what people are feeling on the topic of your choice.</p>
                </div>
                <div style="text-align: end;" class="col-md-2 ml-auto">
                    <p>Powered By</p>
                    <img style="margin: 0.5%" class="box small img-fluid" src="{{asset('assets/images/twitter.png')}}"
                         alt="...">
                </div>
            </div>
        </div>
    </footer>
    <script>
        $(document).ready(function () {
            $("#pageBody").css("background-image", "url('assets/images/mainPage_background.png')");
            $.ajax({
                type: 'GET',
                url: "{{url('/recommendations')}}",
                success: function (topic_recommendations) {
                    for (var topic in topic_recommendations) {
                        $("#recommendation_list").append(
                            "<li class='active'> <a class='recommendedTopic' href='#'>#" + topic_recommendations[topic] + "</a> </li>"
                        )
                    }
                }
            });
        });

        $('body').on('click', 'a.recommendedTopic', function () {
            $("#search_bar").val(($(this).text().substring(1)));
        });

        var ajax_request;

        $('#searchForm').submit(function (e) {
            e.preventDefault();
            var search_val = $('#search_bar').val();
            var mode_val = $('#mode_dropdown').val();

            document.getElementById('cancel-button').style.display = 'inline-block';

            $("#div_for_cancel_button").busyLoad("show", {
                background: "rgba(255,255,255,0.5)",
                image: "{{asset('assets/images/loading.gif')}}",
                maxSize: "500px",
            });

            ajax_request = $.ajax({
                type: 'GET',
                url: "{{url('/submitRequest')}}",
                data: {searchRequest: search_val, modeChoice: mode_val},
                success: function (data) {
                    if ($.isEmptyObject(data.errors)) {
                        window.location.href = '{{url('/visualize')}}' + "?searchRequest=" + search_val + "&modeChoice=" + mode_val;
                    } else {
                        $(".print-error-msg").find("ul").html('');
                        $(".print-error-msg").css('display', 'block');
                        $.each(data.errors, function (key, value) {
                            $(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                        });
                    }
                },
                complete: function () {
                    $("#div_for_cancel_button").busyLoad("hide");
                }
            });
        });
        $("#cancel-button").click(function () {
            ajax_request.abort();
            $("#div_for_cancel_button").busyLoad("hide");
            $("#cancel-button").css('display', 'none');
            document.getElementById('search_bar').value = '';
        });
    </script>
@endsection
