@extends('user.main')
@section('templateContent')
<div class="bg-light">
    <div class="container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb text-capitalize fw-bolder m-0 py-3 fs-5">
              <li class="breadcrumb-item"><a href="#" class="no-active">Trang chủ</a></li>
              <li class="breadcrumb-item active" aria-current="page">Đăng nhập / Đăng ký</li>
            </ol>
          </nav>
    </div>
</div>
@include('admin.core.alert')

<!-- MAIN BODY -->
<main class="p-5 bg-light">
    <div class="mx-auto shadow p-5 bg-white" style="max-width: 450px;">
        <div class="text-center mb-5">
            <img src="{{asset('asset/images/Logo.png') }}" class="w-100">
        </div>
        <div id="forms-container">
            <div id="login-form" class="login" style="display: {{ session('method') == 'register' ? 'none' : 'block' }};">
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="my-3">
                        <div class="mb-2">
                            <div class="input-group mb-2">
                                <input type="text" class="login-input form-control rounded-0" name="first_name" id="first_name" placeholder="Tên đăng nhập" value="{{ old('first_name') }}">
                            </div>                
                        </div>
                        <div class="">
                            <div class="input-group">
                                <input type="password" class="login-input form-control rounded-0" name="password" id="password" placeholder="Mật khẩu">
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-0">Đăng nhập</button>
                        <div class="text-center">
                            <a href="javascript:void(0)" class="fs-6" onclick="toggleForms('register')">Bạn chưa có tài khoản? Đăng ký</a>
                        </div>
                    </div>
                </form>
            </div>

            <div id="register-form" class="register" style="display: {{ session('method') == 'register' ? 'block' : 'none' }};">
                <form action="{{ route('register') }}" method="post">
                    @csrf
                    <div class="my-3">
                        <div class="input-group mb-2">
                            <input type="email" class="form-control rounded-0" name="email" id="email" placeholder="Email" value="{{ old('email') }}">
                        </div>
                        <p class="help is-danger text-danger email">{{ $errors->first('email') }}</p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control rounded-0" name="username" id="username" placeholder="Tên đăng nhập" value="{{ old('username') }}">                
                        </div>
                        <p class="help is-danger text-danger username">{{ $errors->first('username') }}</p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control rounded-0" name="phone" id="phone" placeholder="Số điện thoại" value="{{ old('phone') }}">                
                        </div>
                        <p class="help is-danger text-danger phone">{{ $errors->first('phone') }}</p>
                        <div class="input-group mb-2">
                            <input type="password" class="form-control rounded-0" name="password" id="password" placeholder="Mật khẩu">
                        </div>
                        <p class="help is-danger text-danger password">{{ $errors->first('password') }}</p>
                        <div class="input-group">
                            <input type="password" class="form-control rounded-0" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu">
                        </div>
                        <p class="help is-danger text-danger confirm_password">{{ $errors->first('confirm_password') }}</p>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-0">Đăng Ký</button>
                        <div class="text-center">
                            <a href="javascript:void(0)" class="fs-6" onclick="toggleForms('login')">Bạn đã có tài khoản? Đăng nhập</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    function toggleForms(form) {
        if (form === 'register') {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        } else {
            document.getElementById('login-form').style.display = 'block';
            document.getElementById('register-form').style.display = 'none';
        }
    }
</script>
@endsection
