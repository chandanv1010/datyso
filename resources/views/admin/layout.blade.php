<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{ env('APP_NAME') }} - @yield('headerTitle')</title>
        <link rel="icon" type="image/png" href="{{ url('asset/images/Logo-shortcut.png') }}">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css')}}">
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css')}}">
        <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css')}}">
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
        <link rel="stylesheet" href="{{ asset('backend/plugins/jquery-ui.css') }}">
        <link rel="stylesheet" href="{{ asset('asset/css/fix.css') }}">
        @yield('headerStyle')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <div class="preloader flex-column justify-content-center align-items-center">
                <img class="animation__shake" src="{{ asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
            </div>
            @yield('layout-content')
            @include('admin.core.header')
            @include('admin.core.navbar')
            <div class="content-wrapper">
                @yield('templateContent')
            </div>
            @include('admin.core.footer')
            <aside class="control-sidebar control-sidebar-dark">
            </aside>
        </div>
        <script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>
        <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
        <script>
            $.widget.bridge('uibutton', $.ui.button)
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('plugins/chart.js/Chart.min.js')}}"></script>
        {{-- <script src="{{ asset('plugins/sparklines/sparkline.js')}}"></script> --}}
        <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js')}}"></script>
        <script src="{{ asset('plugins/moment/moment.min.js')}}"></script>
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
        <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
        <script src="{{ asset('dist/js/adminlte.js')}}"></script>
        <script src="{{ asset('dist/js/pages/dashboard.js')}}"></script>
        <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
        <script src="{{ URL::asset('/asset/js/datatable.js') }}" defer></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
        <script src="{{ asset('backend/plugins/ckfinder_2/ckfinder.js') }}"></script>
        <script src="{{ asset('backend/plugins/jquery-ui.js') }}"></script>
        <script src="{{ asset('backend/plugins/jquery.nestable.js') }}"></script>
        <script src="{{ asset('backend/library/finder.js') }}"></script>
        <script src="{{ asset('asset/js/fix.js') }}"></script>
        @yield('footer')
    </body>
</html>
