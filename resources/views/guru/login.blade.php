@extends('layouts.auth')

@section('title', 'Login Guru')

@section('content')
    <!-- Login -->
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">Selamat Datang! 👋</h4>
            <p class="mb-6">Silakan masuk untuk mengelola bank soal.</p>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form id="formAuthentication" class="mb-6" action="{{ route('guru.login.post') }}" method="POST">
                @csrf
                <div class="mb-6 form-control-validation">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                        placeholder="Masukkan username" required autofocus />
                </div>
                <div class="mb-6 form-password-toggle form-control-validation">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="Masukkan password" required />
                        <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                </div>
                <div class="my-8">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0 ms-2">
                            <input class="form-check-input" type="checkbox" id="remember-me" />
                            <label class="form-check-label" for="remember-me"> Ingat Saya </label>
                        </div>
                        <a href="#">
                            <p class="mb-0">Lupa Password?</p>
                        </a>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary d-grid w-100">Masuk</button>
            </form>

            <div class="divider my-6">
                <div class="divider-text">Atau</div>
            </div>

            <div class="d-flex justify-content-center">
                <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-google-plus">
                    <i class="icon-base ti tabler-brand-google-filled icon-20px"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /Login -->
@endsection
