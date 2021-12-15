@section('scripts')
    <!-- jQuery Library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- /jQuery Library -->

    <!-- Search Bar Options -->
    <script src="{{ url('/') }}/assets/js/extention/choices.js"></script>
    <script>
        const choices = new Choices('[data-trigger]',
            {
                searchEnabled: false,
                itemSelectText: '',
            });
    </script>
    <!-- /Search Bar Options -->
@endsection
