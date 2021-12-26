@section('scripts')
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

    <script src="{{ asset('assets/js/sidebar.js') }}"></script>

@endsection
