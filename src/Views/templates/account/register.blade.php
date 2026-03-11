@extends('layout')
@section('content')
    <section class="account-page py-4">
        <div class="max-width">
            <div class="account-box">
                <p class="account-eyebrow">Create Account</p>
                <h1 class="account-title">{{ $titleMain ?? 'Đăng ký tài khoản' }}</h1>
                <p class="account-subtitle">Tạo tài khoản mới để mua hàng nhanh hơn và lưu thông tin tiện lợi.</p>
                @if (!empty($error))
                    <div class="alert alert-danger">{!! $error !!}</div>
                @endif
                <form method="post" action="{{ url('user.register.submit') }}" class="account-form">
                    <div class="mb-3">
                        <label class="form-label" for="fullname-register">Họ tên</label>
                        <input type="text" id="fullname-register" name="fullname" class="form-control"
                            placeholder="Nhập họ tên" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email-register">Email</label>
                        <input type="email" id="email-register" name="email" class="form-control"
                            placeholder="Nhập email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone-register">Số điện thoại</label>
                        <input type="text" id="phone-register" name="phone" class="form-control"
                            placeholder="Nhập số điện thoại" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password-register">Mật khẩu</label>
                        <div class="account-password-wrap">
                            <input type="password" id="password-register" name="password" class="form-control"
                                placeholder="Tối thiểu 6 ký tự" required>
                            <button class="account-password-toggle js-toggle-password" type="button"
                                data-target="#password-register" aria-label="Hiện mật khẩu">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password-confirm-register">Xác nhận mật khẩu</label>
                        <div class="account-password-wrap">
                            <input type="password" id="password-confirm-register" name="password_confirm" class="form-control"
                                placeholder="Nhập lại mật khẩu" required>
                            <button class="account-password-toggle js-toggle-password" type="button"
                                data-target="#password-confirm-register" aria-label="Hiện mật khẩu">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn account-btn w-100">Đăng ký</button>
                </form>
                <p class="account-link">Đã có tài khoản? <a href="{{ url('user.login') }}">Đăng nhập</a></p>
            </div>
        </div>
    </section>
@endsection
