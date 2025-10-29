<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} - Đăng nhập quản trị</title>
    <link rel="icon" type="image/png" href="{{ url('asset/images/Logo-shortcut.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="d-flex justify-content-center mt-5">
        <div class="align-self-center col-xxl-3 col-4 mt-5">
            @include('admin.core.alert')
            <div class="text-center">
                <a class="navbar-brand" href="#">
                    <img style="width: 238px;" src="{{ url('asset/images/Logo.png') }}"
                        alt="Bootstrap" width="100" height="auto">
                </a>
                <div class="d-flex justify-content-center w-100 my-3 text-uppercase"></div>
            </div>
            <div class="border border-0 rounded-5 bg-white p-4 shadow">
                <form action="{{ route('admin.login') }}" method="post">
                    @csrf
                    <h1 class="fs-4 text-center">Đăng nhập hệ thống quản trị</h1>
                    <div class="m-3"></div>
                    <div class="input-group mb-3">
                        <span class="input-group-text border-1 border-opacity-50 rounded-start-pill" id="basic-addon1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                            </svg>
                        </span>
                        <input type="text" class="form-control border-1 border-opacity-50 rounded-end-pill"
                            name="username" placeholder="Tên đăng nhập" value="{{ old('username') }}">
                    </div>
                    <p class="help is-danger text-danger">{{ $errors->first('username') }}</p>
                    <div class="input-group mb-3">
                        <span class="input-group-text rounded-start-pill" id="basic-addon1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                class="bi bi-lock-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                            </svg>
                        </span>
                        <input type="password" class="form-control rounded-end-pill" name="password"
                            placeholder="Mật khẩu">
                    </div>
                    <p class="help is-danger text-danger">{{ $errors->first('password') }}</p>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill" type="button">Đăng nhập</button>
                        <div class="text-center mt-1">
                            <a href="{{ route('admin.resetPassword.create') }}"
                                class="fst-italic text-decoration-none">Quên
                                mật khẩu?</a>
                        </div>
                        <p class="text-center mt-1">Bản quyền ©2015-2024. Giữ toàn quyền.</p>


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
