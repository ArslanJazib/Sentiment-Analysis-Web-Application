@extends('layout.masterLayout')
@section('content')
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid" style="border: none">
            <a class="navbar-brand" href="{{url('/Search')}}">Search Page</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDarkDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink1" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Mode
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark modeList" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                        <a  class="nav-link dropdown-toggle" href="#" id="navbarDarkDropdownMenuLink2" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Graph
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark graphList" aria-labelledby="navbarDarkDropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid bg-dark">
        <div class="row">
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="area_canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="pie_canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <canvas id="polar_canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-dark">
        <div class="card bg-dark">
            <h5 class="card-header">Insight
                <i class="fas fa-lightbulb fa-lg fa-fw" style="color: yellow"></i>
            </h5>
            <div class="card-body">
                <h5 class="card-title">
                    @if ($sentiment['total_positives']>$sentiment['total_negatives'])
                        Sentiment is Positive for {{$sentiment['topic']}}
                    @elseif ($sentiment['total_positives']<$sentiment['total_negatives'])
                        Sentiment is Negative for {{$sentiment['topic']}}
                    @else
                        Sentiment cannot be determined for {{$sentiment['topic']}}
                    @endif
                </h5>
                <p class="card-text">
                    @if ($sentiment['total_positives']>$sentiment['total_negatives'])
                        You should invest in business ventures involving {{$sentiment['topic']}}
                    @elseif ($sentiment['total_positives']<$sentiment['total_negatives'])
                        You should not invest in business ventures involving {{$sentiment['topic']}}
                    @else
                        Sentiment is inconclusive
                    @endif
                </p>
            </div>
        </div>
    </div>
    <script>
        var barChartData = {
            labels: ['Positive', 'Negative'],
            datasets: [
                {
                    label: 'Bar Chart',
                    backgroundColor: ['rgba(255,99,132,0.2)','rgba(0, 195, 71,0.2)'],
                    borderColor: ['rgba(255,99,132,1)','rgba(0, 195, 71,1)'],
                    borderWidth: 1,
                    hoverBackgroundColor: [
                        'rgba(255,99,132,1)',
                        'rgba(0, 195, 71,1)'
                    ],
                    hoverBorderColor: ['rgba(255,99,132,1)','rgba(0, 195, 71,0.4)'],
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
                    backgroundColor: ['rgba(255,99,132,0.2)','rgba(0, 195, 71,0.2)'],
                    borderColor: 'rgba(0, 195, 71,1)',
                    borderCapStyle: 'round',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: ['#ffffff','#ffffff'],
                    pointBackgroundColor: '#ffffff',
                    hoverBackgroundColor: ['rgba(0, 195, 71,1)'],
                    pointBorderWidth: 3,
                    pointHoverRadius: 10,
                    pointHoverBackgroundColor: ['rgba(255,99,132,1)','rgba(0, 195, 71,1)'],
                    pointHoverBorderColor: ['rgba(255,99,132,1)','rgba(0, 195, 71,1)'],
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
        var polarChartData={
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
        window.onload = function() {
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
                            },
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: "white",
                            }
                        }]
                    }
                }

            });
            var ctx2 = document.getElementById("area_canvas").getContext("2d");
            window  = new Chart(ctx2, {
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
            window  = new Chart(ctx3, {
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
                            }
                        }]
                    }
                }
            });
            var ctx4 = document.getElementById("polar_canvas").getContext("2d");
            window  = new Chart(ctx4, {
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
                            }
                        }]
                    }
                }
            });
        };
    </script>
@endsection
