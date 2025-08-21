@extends('layouts.app')

@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="login-register container">
            <ul class="nav nav-tabs mb-5" id="login_register" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link nav-link_underscore active" id="login-tab" data-bs-toggle="tab" href="#tab-item-login"
                        role="tab" aria-controls="tab-item-login" aria-selected="true">Login</a>
                </li>
            </ul>
            <div class="tab-content pt-2" id="login_register_tab_content">
                <div class="tab-pane fade show active" id="tab-item-login" role="tabpanel" aria-labelledby="login-tab">
                    <div class="login-form">
                        <form method="POST" action="{{ route('login') }}" name="login-form" class="needs-validation" novalidate="">
                            @csrf

                            <div class="form-floating mb-3">
                                <input id="email" type="email" class="form-control form-control_gray @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required="" autocomplete="email" autofocus="">
                                <label for="email">Email address *</label>
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pb-3"></div>

                            <div class="form-floating mb-3">
                                <input id="password" type="password" class="form-control form-control_gray @error('password') is-invalid @enderror"
                                       name="password" required="" autocomplete="current-password">
                                <label for="password">Password *</label>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="btn-text">Forgot Password?</a>
                                @endif
                            </div>

                            <button class="btn btn-primary w-100 text-uppercase" type="submit">Log In</button>

                            <div class="customer-option mt-4 text-center">
                                <span class="text-secondary">No account yet?</span>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn-text js-show-register">Create Account</a>
                                @endif
                                @auth
                                    | <a href="{{ route('user.index') }}" class="btn-text">My Account</a>
                                @endauth
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
