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

    <div class="s013">
        <form method="get" action="{{url('/submitRequest')}}" id="searchForm">
            @csrf
            <div class="inner-form">
                <div class="left">
                    <div class="input-wrap first">
                        <div class="input-field first">
                            <label><i class="fas fa-search fa-fw" style="color: white"></i>Topic</label>
                            <input id="search_bar" name="searchRequest" type="text" placeholder="ex: Artificial Intelligence, Machine Learning, Python"/>
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
    <script>
        {{--$('#searchForm').submit(function(e){--}}
        {{--    e.preventDefault();--}}
        {{--    var search_val=$('#search_bar').val();--}}
        {{--    var mode_val=$('#mode_dropdown').val();--}}
        {{--    $.ajax({--}}
        {{--        type: 'GET',--}}
        {{--        url: "{{url('/submitRequest')}}",--}}
        {{--        data: {searchRequest : search_val,modeChoice : mode_val},--}}
        {{--        success: function () {--}}
        {{--            window.location.href='{{url('/visualize')}}'+"?searchRequest="+search_val;--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}
    </script>
@endsection
