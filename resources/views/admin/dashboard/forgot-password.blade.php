<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} - Quên mật khẩu quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    @include('admin.core.alert')
    <div class="d-flex justify-content-center h-100 mt-5">
        <div class="align-self-center col-xxl-3 col-4">
            <div class="text-center">
                <a class="navbar-brand" href="#">
                    <img style="width: 238px;" src="{{asset('/asset/images/Logo.png')}}"
                        alt="Bootstrap" width="100" height="auto">
                </a>
                <div class="d-flex justify-content-center w-100 my-3 text-uppercase">
                </div>
            </div>
            <div class="border border-0 rounded-5 bg-white p-4 shadow">
                <form action="{{ route('admin.resetPassword.create') }}" method="post">
                    @csrf
                    <h1 class="fs-4 text-center">Quên mật khẩu</h1>
                    <div class="m-3"></div>
                    <div class="input-group mb-3">
                        <span class="input-group-text rounded-start-pill" id="basic-addon1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-envelope-at-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2 2A2 2 0 0 0 .05 3.555L8 8.414l7.95-4.859A2 2 0 0 0 14 2H2Zm-2 9.8V4.698l5.803 3.546L0 11.801Zm6.761-2.97-6.57 4.026A2 2 0 0 0 2 14h6.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586l-1.239-.757ZM16 9.671V4.697l-5.803 3.546.338.208A4.482 4.482 0 0 1 12.5 8c1.414 0 2.675.652 3.5 1.671Z" />
                                <path
                                    d="M15.834 12.244c0 1.168-.577 2.025-1.587 2.025-.503 0-1.002-.228-1.12-.648h-.043c-.118.416-.543.643-1.015.643-.77 0-1.259-.542-1.259-1.434v-.529c0-.844.481-1.4 1.26-1.4.585 0 .87.333.953.63h.03v-.568h.905v2.19c0 .272.18.42.411.42.315 0 .639-.415.639-1.39v-.118c0-1.277-.95-2.326-2.484-2.326h-.04c-1.582 0-2.64 1.067-2.64 2.724v.157c0 1.867 1.237 2.654 2.57 2.654h.045c.507 0 .935-.07 1.18-.18v.731c-.219.1-.643.175-1.237.175h-.044C10.438 16 9 14.82 9 12.646v-.214C9 10.36 10.421 9 12.485 9h.035c2.12 0 3.314 1.43 3.314 3.034v.21Zm-4.04.21v.227c0 .586.227.8.581.8.31 0 .564-.17.564-.743v-.367c0-.516-.275-.708-.572-.708-.346 0-.573.245-.573.791Z" />
                            </svg>
                        </span>
                        <input type="text" class="form-control border-1 border-opacity-50 rounded-end-pill"
                            name="email" placeholder="Nhập email" value="{{ old('email') }}">
                    </div>
                    <p class="help is-danger text-danger">{{ $errors->first('email') }}</p>

                    <span>Bạn đã có tài khoản <a href="{{ route('admin.login') }}">Đăng nhập</a></span>
                    <div class="d-grid gap-2 mt-2">
                        <button type="submit" class="btn btn-lg btn-primary btn-block">
                            Gửi
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
