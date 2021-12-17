@extends('layout.masterLayout')
@section('content')
    <nav style="border-bottom: 1px solid white !important;" class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid" style="border: none">
            <a class="navbar-brand" href="{{url('/Search')}}"><i class="fas fa-search"></i></a>
            <span class="navbar-brand">Visualization Page</span>
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                <ul class="navbar-nav">
                    <a style="font-weight: bold" id="popup-modal" class="btn btn-warning" href="#test-modal">Insight <i
                            class="fas fa-lightbulb"></i></a>
                    <div id="test-modal" class="white-popup-block mfp-hide">
                        <h1>
                            @if ($sentiment['total_positives']>$sentiment['total_negatives'])
                                Sentiment is Positive for {{$sentiment['topic']}}
                                <i style="color: gold" class="fas fa-smile"></i>
                            @elseif ($sentiment['total_positives']<$sentiment['total_negatives'])
                                Sentiment is Negative for {{$sentiment['topic']}}
                                <i style="color: lightblue" class="fas fa-frown-open"></i>
                            @else
                                Sentiment cannot be determined for {{$sentiment['topic']}}
                            @endif
                        </h1>
                        <p>
                            @if ($sentiment['total_positives']>$sentiment['total_negatives'])
                                You should invest in business ventures involving {{$sentiment['topic']}}
                            @elseif ($sentiment['total_positives']<$sentiment['total_negatives'])
                                You should not invest in business ventures involving {{$sentiment['topic']}}
                            @else
                                Sentiment is inconclusive
                            @endif
                        </p>
                        <p><a class="btn btn-danger popup-modal-dismiss" href="#">Close</a></p>
                    </div>
                    <div style="margin-left: 10px" class="btn-group">
                        <button style="color: black;font-weight: bold" id="modebtn" type="button"
                                class="btn btn-primary">{{$sentiment['mode']}}</button>
                        <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul id="modeList" class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">General</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#">Start-Up</a></li>
                        </ul>
                    </div>
                    <div style="margin-left: 10px" class="btn-group">
                        <button style="color: black;font-weight: bold" id="topicbtn" type="button"
                                class="btn btn-success">{{"#".$sentiment['topic']}}
                        </button>
                        <button style="background-color: darkgreen" type="button"
                                class="btn dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul id="topicList" class="dropdown-menu">
                            @foreach ($sentiment['previousTopics'] as $topic)
                                <li><a class="dropdown-item" href="{{url('/topicVisualizationData').'?searchRequest=' . $sentiment['topic'] . "&topicChoice=" . $topic['topic_id']."&modeChoice=" . $sentiment['mode']}}">{{$topic['topic']}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </ul>
            </div>
            <img class="box small" src="{{asset('assets/images/twitter.png')}}" class="img-fluid" alt="...">
        </div>
    </nav>
    <div style="height: 90vh" class="container-fluid bg-dark">
        <div class="row">
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="canvas" height="250" width="600"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="area_canvas" height="250" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="pie_canvas" height="250" width="600"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="polar_canvas" height="250" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
{{--        <div class="row">--}}
{{--            <div class="col-12">--}}
{{--                <div class="panel panel-default">--}}
{{--                    <div class="panel-body">--}}
{{--                        <canvas id="line_canvas" height="250" width="600px"></canvas>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
    <script>
        $(function () {
            $('#popup-modal').magnificPopup({
                type: 'inline',
                preloader: false,
                focus: '#username',
                modal: true
            });
            $(document).on('click', '.popup-modal-dismiss', function (e) {
                e.preventDefault();
                $.magnificPopup.close();
            });
            $("#modeList li a").click(function () {
                $("#modebtn:first-child").text($(this).text());
                $("#modebtn:first-child").val($(this).text());
            });
            $("#topicList li a").click(function () {
                $("#topicbtn:first-child").text($(this).text());
                $("#topicbtn:first-child").val($(this).text());
            });
        });

        $("#modebtn").click(function () {
            var search_val = "{{$sentiment['topic']}}";
            var mode_val = $("#modebtn").text();

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
                    window.location.href = '{{url('/visualize')}}' + "?searchRequest=" + search_val + "&modeChoice=" + mode_val;
                },
                complete: function () {
                    $("#pageBody").busyLoad("hide");
                }
            });
        });

        var barChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [
                {
                    label: 'Bar Chart',
                    backgroundColor: ['rgba(255,99,132,0.2)', 'rgba(0, 195, 71,0.2)'],
                    borderColor: ['rgba(255,99,132,1)', 'rgba(0, 195, 71,1)'],
                    borderWidth: 1,
                    hoverBackgroundColor: [
                        'rgba(255,99,132,1)',
                        'rgba(0, 195, 71,1)'
                    ],
                    hoverBorderColor: ['rgba(255,99,132,1)', 'rgba(0, 195, 71,0.4)'],
                    borderCapStyle: 'round',
                    data: [{{ $sentiment['total_positives'] }}, {{ $sentiment['total_negatives'] }}]
                }
            ]
        };
        var areaChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [
                {
                    label: 'Area Chart',
                    borderWidth: 1,
                    fill: true,
                    lineTension: 0.1,
                    backgroundColor: ['rgba(255,99,132,0.2)', 'rgba(0, 195, 71,0.2)'],
                    borderColor: 'rgba(0, 195, 71,1)',
                    borderCapStyle: 'round',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: ['#ffffff', '#ffffff'],
                    pointBackgroundColor: '#ffffff',
                    hoverBackgroundColor: ['rgba(0, 195, 71,1)'],
                    pointBorderWidth: 3,
                    pointHoverRadius: 10,
                    pointHoverBackgroundColor: ['rgba(255,99,132,1)', 'rgba(0, 195, 71,1)'],
                    pointHoverBorderColor: ['rgba(255,99,132,1)', 'rgba(0, 195, 71,1)'],
                    pointHoverBorderWidth: 2,
                    pointRadius: 2,
                    pointHitRadius: 10,
                    data: [{{ $sentiment['total_positives'] }}, {{ $sentiment['total_negatives'] }}]
                }
            ]
        };
        var pieChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [{
                data: [{{ $sentiment['total_positives'] }}, {{ $sentiment['total_negatives'] }}],
                backgroundColor: [
                    'rgba(255,99,132,0.2)',
                    'rgba(0, 195, 71,0.2)'
                ],
                hoverBackgroundColor: [
                    'rgba(255,99,132,1)',
                    'rgba(0, 195, 71,1)'
                ],
            }]
        };
        var polarChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [
                {
                    backgroundColor: [
                        'rgba(255,99,132,0.2)',
                        'rgba(0, 195, 71,0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(0, 195, 71,1)'
                    ],
                    hoverBackgroundColor: [
                        'rgba(255,99,132,1)',
                        'rgba(0, 195, 71,1)'
                    ],
                    data: [{{ $sentiment['total_positives'] }}, {{ $sentiment['total_negatives'] }}]
                }
            ]
        }
        {{--var lineChartData = {--}}
        {{--    labels: ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],--}}
        {{--    datasets: [--}}
        {{--        {--}}
        {{--            backgroundColor: [--}}
        {{--                'rgba(255,99,132,0.2)',--}}
        {{--                'rgba(0, 195, 71,0.2)'--}}
        {{--            ],--}}
        {{--            borderColor: [--}}
        {{--                'rgba(255,99,132,1)',--}}
        {{--                'rgba(0, 195, 71,1)'--}}
        {{--            ],--}}
        {{--            hoverBackgroundColor: [--}}
        {{--                'rgba(255,99,132,1)',--}}
        {{--                'rgba(0, 195, 71,1)'--}}
        {{--            ],--}}
        {{--            data: [--}}
        {{--                {{ $sentiment['daySentiment']['Mon'] }},--}}
        {{--                {{ $sentiment['daySentiment']['Tue'] }},--}}
        {{--                {{ $sentiment['daySentiment']['Wed'] }},--}}
        {{--                {{ $sentiment['daySentiment']['Thu'] }},--}}
        {{--                {{ $sentiment['daySentiment']['Fri'] }},--}}
        {{--                {{ $sentiment['daySentiment']['Sat'] }},--}}
        {{--                {{ $sentiment['daySentiment']['Sun'] }}--}}
        {{--            ]--}}
        {{--        }--}}
        {{--    ]--}}
        {{--}--}}
        window.onload = function () {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                responsive: true,
                maintainAspectRatio: true,
                title: {
                    display: true,
                    text: 'Bar Chart'
                },
                options: {
                    legend: {
                        labels: {
                            fontColor: "white"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                fontColor: "white",
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: "white",
                            },
                            gridLines: {
                                color: 'rgba(255,255,255,1)',
                                lineWidth: 1
                            }
                        }]
                    }
                }

            });
            var ctx2 = document.getElementById("area_canvas").getContext("2d");
            window = new Chart(ctx2, {
                type: 'line',
                data: areaChartData,
                responsive: true,
                maintainAspectRatio: true,
                title: {
                    display: true,
                    text: 'Area Chart'
                },
                options: {
                    legend: {
                        labels: {
                            fontColor: "white"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                fontColor: "white",
                                beginAtZero: true
                            },
                            gridLines: {
                                color: 'rgba(255,255,255,1)',
                                lineWidth: 1
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: "white",
                            }
                        }]
                    }
                }

            });
            var ctx3 = document.getElementById("pie_canvas").getContext("2d");
            window = new Chart(ctx3, {
                type: 'pie',
                data: pieChartData,
                responsive: true,
                maintainAspectRatio: true,
                title: {
                    display: true,
                    text: 'Pie Chart'
                },
                options: {
                    legend: {
                        labels: {
                            fontColor: "white"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                display: false
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                display: false
                            },
                            gridLines: {
                                color: 'rgba(255,255,255,1)',
                                lineWidth: 1
                            }
                        }]
                    }
                }
            });
            var ctx4 = document.getElementById("polar_canvas").getContext("2d");
            window = new Chart(ctx4, {
                type: 'polarArea',
                data: polarChartData,
                responsive: true,
                maintainAspectRatio: true,
                title: {
                    display: true,
                    text: 'Polar Chart'
                },
                options: {
                    legend: {
                        labels: {
                            fontColor: "white"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                display: false
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                display: false
                            },
                            gridLines: {
                                color: 'rgba(255,255,255,1)',
                                lineWidth: 1
                            }
                        }],
                        r: {
                            grid: {
                                color: 'rgba(255,255,255,1)'
                            }
                        }
                    }
                }
            });
            // var ctx5 = document.getElementById("line_canvas").getContext("2d");
            // window = new Chart(ctx5, {
            //     type: 'line',
            //     data: lineChartData,
            //     responsive: true,
            //     maintainAspectRatio: true,
            //     title: {
            //         display: true,
            //         text: 'Line Chart'
            //     },
            //     options: {
            //         legend: {
            //             labels: {
            //                 fontColor: "white"
            //             }
            //         },
            //         scales: {
            //             yAxes: [{
            //                 ticks: {
            //                     display: false
            //                 }
            //             }],
            //             xAxes: [{
            //                 ticks: {
            //                     display: false
            //                 },
            //                 gridLines: {
            //                     color: 'rgba(255,255,255,1)',
            //                     lineWidth: 1
            //                 }
            //             }],
            //             r: {
            //                 grid: {
            //                     color: 'rgba(255,255,255,1)'
            //                 }
            //             }
            //         }
            //     }
            // });
        };
    </script>
@endsection
