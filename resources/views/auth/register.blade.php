@extends('layouts.app')

@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="login-register container">
            <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link nav-link_underscore active" id="register-tab" data-bs-toggle="tab"
                        href="#tab-item-register" role="tab" aria-controls="tab-item-register"
                        aria-selected="true">Register</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link nav-link_underscore" id="login-tab" data-bs-toggle="tab"
                        href="#tab-item-login" role="tab" aria-controls="tab-item-login"
                        aria-selected="false">Login</a>
                </li>
            </ul>
            <div class="tab-content pt-2" id="login_register_tab_content">
                <!-- Registration Tab -->
                <div class="tab-pane fade show active" id="tab-item-register" role="tabpanel"
                    aria-labelledby="register-tab">
                    <div class="register-form">
                        <form method="POST" action="{{ route('register') }}" name="register-form" class="needs-validation" novalidate="">
                            @csrf

                            <div class="form-floating mb-3">
                                <input class="form-control form-control_gray @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required="" autocomplete="name" autofocus="">
                                <label for="name">Name *</label>
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pb-3"></div>

                            <div class="form-floating mb-3">
                                <input id="email" type="email" class="form-control form-control_gray @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required="" autocomplete="email">
                                <label for="email">Email address *</label>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pb-3"></div>

                            <div class="form-floating mb-3">
                                <input id="mobile" type="text" class="form-control form-control_gray @error('mobile') is-invalid @enderror"
                                       name="mobile" value="{{ old('mobile') }}" required="" autocomplete="mobile">
                                <label for="mobile">Mobile *</label>
                                @error('mobile')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pb-3"></div>

                            <div class="form-floating mb-3">
                                <input id="password" type="password" class="form-control form-control_gray @error('password') is-invalid @enderror"
                                       name="password" required="" autocomplete="new-password">
                                <label for="password">Password *</label>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-floating mb-3">
                                <input id="password-confirm" type="password" class="form-control form-control_gray"
                                       name="password_confirmation" required="" autocomplete="new-password">
                                <label for="password-confirm">Confirm Password *</label>
                            </div>

                            <div class="d-flex align-items-center mb-3 pb-2">
                                <p class="m-0">Your personal data will be used to support your experience throughout this
                                    website, to manage access to your account, and for other purposes described in our privacy policy.
                                </p>
                            </div>

                            <button class="btn btn-primary w-100 text-uppercase" type="submit">Register</button>

                            <div class="customer-option mt-4 text-center">
                                <span class="text-secondary">Have an account?</span>
                                <a href="#tab-item-login" class="btn-text" data-bs-toggle="tab" role="tab">Login to your Account</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Login Tab -->
                <div class="tab-pane fade" id="tab-item-login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="login-form">
                        <form method="POST" action="{{ route('login') }}" name="login-form" class="needs-validation" novalidate="">
                            @csrf

                            <div class="form-floating mb-3">
                                <input id="login_email" type="email" class="form-control form-control_gray @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required="" autocomplete="email" autofocus="">
                                <label for="login_email">Email address *</label>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pb-3"></div>

                            <div class="form-floating mb-3">
                                <input id="login_password" type="password" class="form-control form-control_gray @error('password') is-invalid @enderror"
                                       name="password" required="" autocomplete="current-password">
                                <label for="login_password">Password *</label>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center mb-3 pb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 text-uppercase" type="submit">Login</button>

                            <div class="customer-option mt-4 text-center">
                                <span class="text-secondary">Don't have an account?</span>
                                <a href="#tab-item-register" class="btn-text" data-bs-toggle="tab" role="tab">Create Account</a>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-center mt-3">
                                    <a href="{{ route('password.request') }}" class="btn-text">Forgot Your Password?</a>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
