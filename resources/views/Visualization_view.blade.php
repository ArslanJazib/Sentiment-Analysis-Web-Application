@extends('layout.masterLayout')
@section('content')
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
                    <span class="navbar-brand">Visualization Page</span>
                </div>
            </div>
        </div>
    </nav>
    <div id="div_for_cancel_button" class="wrapper d-flex align-items-stretch">
        <nav style="min-width: 180px;max-width: 180px" id="sidebar">
            <div class="custom-menu">
                <button type="button" id="visualsidebarCollapse" class="btn btn-primary">
                    <i style="color: black" class="fas fa-lightbulb"></i>
                    <span class="sr-only">Toggle Menu</span>
                </button>
            </div>
            <div>
                <ul class="list-unstyled components mb-5">
                    <!-- Insight-->
                    <li class="active">
                        <!-- Insight Modal Anchor Tag -->
                        <a class="dashTool" style="color:white;font-weight: bold" id="popup-modal" href="#test-modal">
                            <div class="row">
                                <div class="col-1">
                                    <i style="color:yellow " class="fas fa-lightbulb fa-fw fa-lg"></i>
                                </div>
                                <div class="col">
                                    <span>Insight</span>
                                </div>
                            </div>
                        </a>
                        <!-- Insight Modal -->
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
                    </li>
                    <!-- Mode -->
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <span class="accordion-header" id="flush-headingOne">
                                <a class="accordion" type="button" data-bs-toggle="collapse"
                                   data-bs-target="#flush-collapseOne" aria-expanded="false"
                                   aria-controls="flush-collapseOne">
                                    <div class="row">
                                        <div class="col-1">
                                            <img id="modeIcon"
                                                 src="{{asset('assets/images/modeIcon.png')}}"
                                                 alt="...">
                                        </div>
                                        <div class="col">
                                            <span>
                                                Modes
                                            </span>
                                        </div>
                                        <div style="text-align: end" class="col">
                                            <span>
                                                <i class="fa fa-caret-down" style="color:lightseagreen"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </span>
                            <div id="flush-collapseOne" class="accordion-collapse collapse"
                                 aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <ul class="list">
                                        <li style="cursor: pointer" class="list-item">
                                            <a id="generalTag" class="dashTool">
                                                <img style="margin: 0.5%" class="box small img-fluid"
                                                     src="{{asset('assets/images/group.png')}}" alt="...">
                                                General
                                            </a>
                                        </li>
                                        <li style="cursor: pointer" class="list-item">
                                            <a id="startupTag" class="dashTool">
                                                <img style="margin: 0.5%" class="box small img-fluid"
                                                     src="{{asset('assets/images/enterpreneur.png')}}" alt="...">
                                                Start-Up
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Topics -->
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <span class="accordion-header" id="flush-headingOne">
                                <a class="accordion" type="button"
                                   data-bs-toggle="collapse"
                                   data-bs-target="#flush-collapseTwo"
                                   aria-expanded="false"
                                   aria-controls="flush-collapseTwo">
                                    <div class="row">
                                        <div class="col-1">
                                            <img id="topicIcon"
                                                 src="{{asset('assets/images/hashtag.png')}}"
                                                 alt="...">
                                        </div>
                                        <div class="col">
                                            <span>
                                                Topics
                                            </span>
                                        </div>
                                        <div style="text-align: end;" class="col">
                                            <span style="padding-left: 10px">
                                                <i class="fa fa-caret-down" style="color:lightseagreen"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </span>
                            <div id="flush-collapseTwo" class="accordion-collapse collapse"
                                 aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <ul id="topicList" class="list">
                                        @foreach ($sentiment['previousTopics'] as $topic)
                                            @if($sentiment['mode']=="Start-Up")
                                                @if(str_contains($topic['topic'], '#StartUp'))
                                                    <li style="cursor: pointer">
                                                        <a class="dashTool"
                                                           href="{{url('/topicVisualizationData').'?searchRequest=' . $sentiment['topic'] . "&topicChoice=" . $topic['topic_id']."&modeChoice=" . $sentiment['mode']}}">{{$topic['topic']}}</a>
                                                    </li>
                                                @endif
                                            @elseif($sentiment['mode']=="General")
                                                @if(!str_contains($topic['topic'], '#StartUp'))
                                                    <li style="cursor: pointer">
                                                        <a class="dashTool"
                                                           href="{{url('/topicVisualizationData').'?searchRequest=' . $sentiment['topic'] . "&topicChoice=" . $topic['topic_id']."&modeChoice=" . $sentiment['mode']}}">{{$topic['topic']}}</a>
                                                    </li>
                                                @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </nav>
        <!-- visualizations -->
        <div id="graphs" style="margin-left: 3%" class="container-fluid">
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
            <div class="row">
                <div class="col-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <canvas id="line_canvas" height="250" width="600px"></canvas>
                        </div>
                    </div>
                </div>
            </div>
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
            if ("{{$sentiment['mode']}}" === "General") {
                $("#generalTag").css("color", "yellow");
            } else if ("{{$sentiment['mode']}}" === "Start-Up") {
                $("#startupTag").css("color", "yellow");
            }
            $("#currentTopic").css("color", "yellow");

            var listItems = $("#topicList li");
            listItems.each(function (idx, li) {
                var product = $(li);
                var anchorTag = (product.find("a"));
                var text = anchorTag.text();
                if (text === "#{{$sentiment['topic']}}") {
                    anchorTag.css("color", "yellow");
                }
            });
        });

        var ajax_request;

        $("#generalTag").click(function () {

            var search_val = "{{$sentiment['topic']}}";
            var tempVal = search_val.substring(0, search_val.indexOf("#") - 1);
            if (tempVal !== "") {
                search_val = tempVal;
            }
            var mode_val = "General";

            $("#div_for_cancel_button").busyLoad("show", {
                background: "rgba(255,255,255,0.5)",
                image: "{{asset('assets/images/visualLoading.gif')}}",
                maxSize: "100%",
            });

            document.getElementById('cancel-button').style.display = 'inline-block';

            ajax_request = $.ajax({
                type: 'GET',
                url: "{{url('/submitRequest')}}",
                data: {searchRequest: search_val, modeChoice: mode_val},
                success: function () {
                    window.location.href = '{{url('/visualize')}}' + "?searchRequest=" + search_val + "&modeChoice=" + mode_val;
                },
                complete: function () {
                    $("#div_for_cancel_button").busyLoad("hide");
                }
            });
        });

        $("#startupTag").click(function () {
            var search_val = "{{$sentiment['topic']}}";
            console.log(search_val);
            var mode_val = "Start-Up";
            $("#div_for_cancel_button").busyLoad("show", {
                background: "rgba(255,255,255,0.5)",
                image: "{{asset('assets/images/visualLoading.gif')}}",
                maxSize: "100%",
            });
            document.getElementById('cancel-button').style.display = 'inline-block';

            ajax_request = $.ajax({
                type: 'GET',
                url: "{{url('/submitRequest')}}",
                data: {searchRequest: search_val, modeChoice: mode_val},
                success: function () {
                    window.location.href = '{{url('/visualize')}}' + "?searchRequest=" + search_val + "&modeChoice=" + mode_val;
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
        });

        var barChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [
                {
                    label: 'Bar Chart',
                    backgroundColor: ['rgba(0, 195, 71,0.5)', 'rgba(139,0,0,0.5)'],
                    borderColor: ['rgba(0, 195, 71,1)', 'rgba(139,0,0,1)'],
                    borderWidth: 1,
                    hoverBackgroundColor: [
                        'rgba(0, 195, 71,1)',
                        'rgba(139,0,0,1)'
                    ],
                    hoverBorderColor: ['rgba(0, 195, 71,1)', 'rgba(139,0,0,1)'],
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
                    backgroundColor: ['rgba(255,201,0,0.5)'],
                    borderColor: 'rgba(0, 0, 0,1)',
                    borderCapStyle: 'round',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: ['rgba(0, 195, 71,1)', 'rgba(139,0,0,1)'],
                    pointBackgroundColor: ['rgba(0, 195, 71,1)', 'rgba(139,0,0,1)'],
                    hoverBackgroundColor: ['rgba(0, 195, 71,1)'],
                    pointBorderWidth: 3,
                    pointHoverRadius: 10,
                    pointHoverBackgroundColor: ['rgba(0, 195, 71,0.5)', 'rgba(139,0,0,0.5)'],
                    pointHoverBorderColor: ['rgba(0, 195, 71,1)', 'rgba(139,0,0,1)'],
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
                    'rgba(0, 195, 71,0.5)',
                    'rgba(139,0,0,0.5)'
                ],
                hoverBackgroundColor: [
                    'rgba(0, 195, 71,1)',
                    'rgba(139,0,0,1)'
                ],
                borderColor: ['rgba(0, 195, 71,1)', 'rgba(139,0,0,1)'],
            }]
        };
        var polarChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [
                {
                    backgroundColor: [
                        'rgba(0, 195, 71,0.5)',
                        'rgba(139,0,0,0.5)'
                    ],
                    borderColor: [
                        'rgba(0, 195, 71,1)',
                        'rgba(139,0,0,1)'
                    ],
                    hoverBackgroundColor: [
                        'rgba(0, 195, 71,1)',
                        'rgba(139,0,0,1)'
                    ],
                    data: [{{ $sentiment['total_positives'] }}, {{ $sentiment['total_negatives'] }}]
                }
            ]
        }
        var lineChartData = {
            labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
            datasets: [
                {
                    label: 'Timeline 0:Neutral, 1:Negative, 2:Positive',
                    backgroundColor: [
                        'rgba(255,201,0,0.5)',
                    ],
                    borderColor: [
                        'rgba(0,0,0,1)',
                    ],

                    pointBorderColor: 'rgba(255,99,132,1)',
                    pointBackgroundColor: 'rgba(255,99,132,0.5)',
                    hoverBackgroundColor: 'rgba(255,99,132,0.5)',
                    pointBorderWidth: 3,
                    pointHoverRadius: 10,
                    pointHoverBackgroundColor: 'rgba(255,99,132,0.5)',
                    pointHoverBorderColor: 'rgba(255,99,132,1)',
                    pointHoverBorderWidth: 2,
                    pointRadius: 2,
                    pointHitRadius: 10,
                    data: [
                        {{ $sentiment['daySentiment']['Mon'] }},
                        {{ $sentiment['daySentiment']['Tue'] }},
                        {{ $sentiment['daySentiment']['Wed'] }},
                        {{ $sentiment['daySentiment']['Thu'] }},
                        {{ $sentiment['daySentiment']['Fri'] }},
                        {{ $sentiment['daySentiment']['Sat'] }},
                        {{ $sentiment['daySentiment']['Sun'] }}
                    ]
                }
            ]
        }

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
                            fontColor: "black"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                fontColor: "black",
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: "black",
                            },
                            gridLines: {
                                color: 'rgba(0,0,0,1)',
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
                            fontColor: "black"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                fontColor: "black",
                                beginAtZero: true
                            },
                            gridLines: {
                                color: 'rgba(0,0,0,1)',
                                lineWidth: 1
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: "black",
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
                            fontColor: "black"
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
                                color: 'rgba(0,0,0,1)',
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
                            fontColor: "black"
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
                                color: 'rgba(0,0,0,1)',
                                lineWidth: 1
                            }
                        }],
                        r: {
                            grid: {
                                color: 'rgba(0,0,0,1)'
                            }
                        }
                    }
                }
            });
            var ctx5 = document.getElementById("line_canvas").getContext("2d");
            window = new Chart(ctx5, {
                type: 'line',
                data: lineChartData,
                responsive: true,
                maintainAspectRatio: true,
                title: {
                    display: true,
                    text: 'Line Chart'
                },
                options: {
                    legend: {
                        labels: {
                            fontColor: "black"
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0,
                                max: 2,
                                stepSize: 1,
                                fontColor: "black",
                                display: true
                            }
                        }],
                        xAxes: [{

                            ticks: {
                                fontColor: "black",
                                display: true
                            },
                            gridLines: {
                                color: 'rgba(0,0,0,1)',
                                lineWidth: 1
                            }
                        }],
                        r: {
                            grid: {
                                color: 'rgba(0,0,0,1)'
                            }
                        }
                    }
                }
            });
        };
    </script>
@endsection
