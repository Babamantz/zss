@push('css')
    <style>
        :root {
            /* =========================================
               ZHC-INSPIRED BRAND PALETTE
               ========================================= */
            --zhc-blue: #1f4e9e;
            --zhc-blue-dark: #163a78;
            --zhc-blue-light: #eaf1fb;

            --zhc-orange: #f28c28;
            --zhc-orange-dark: #d97418;

            --zhc-white: #ffffff;
            --zhc-light: #f5f7fa;

            --zhc-text: #26364a;
            --zhc-muted: #718096;
            --zhc-border: #dce3ec;
        }

        /* =========================================
           PAGE
           ========================================= */

        body {
            background: var(--zhc-light);
        }

        /* =========================================
           IMAGE SIDE
           ========================================= */

        .zss-image-side {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }

        .zss-image-side .bg-img-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        /*
         * Blue ZHC-style overlay
         */
        .zss-image-side::after {
            content: "";
            position: absolute;
            inset: 0;

            background: linear-gradient(135deg,
                    rgba(22, 58, 120, 0.82) 0%,
                    rgba(31, 78, 158, 0.58) 48%,
                    rgba(31, 78, 158, 0.12) 100%);

            pointer-events: none;
        }

        /*
         * Orange accent line
         */
        .zss-image-side::before {
            content: "";
            position: absolute;

            z-index: 2;

            left: 0;
            bottom: 0;

            width: 100%;
            height: 7px;

            background: var(--zhc-orange);
        }

        /* =========================================
           LOGIN AREA
           ========================================= */

        .zss-login-area {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 45px;

            background:
                radial-gradient(circle at top right,
                    rgba(31, 78, 158, 0.07),
                    transparent 35%),
                var(--zhc-light);
        }

        /* =========================================
           LOGIN CARD
           ========================================= */

        .login-card {
            width: 100%;
            max-width: 470px;

            padding: 42px 40px;

            background: var(--zhc-white);

            border-radius: 12px;

            border-top: 5px solid var(--zhc-blue);

            box-shadow: 0 15px 45px rgba(22, 58, 120, 0.12);

            position: relative;
        }

        /*
         * Small orange accent
         */
        .login-card::after {
            content: "";

            position: absolute;

            top: 0;
            right: 35px;

            width: 55px;
            height: 5px;

            background: var(--zhc-orange);

            border-radius: 0 0 5px 5px;
        }

        /* =========================================
           FORM HEADER
           ========================================= */

        .theme-form.login-form h4 {
            color: var(--zhc-blue-dark);

            font-size: 27px;
            font-weight: 700;

            margin-bottom: 8px;
        }

        .theme-form.login-form h4::after {
            content: "";

            display: block;

            width: 52px;
            height: 4px;

            margin-top: 10px;

            background: var(--zhc-orange);

            border-radius: 10px;
        }

        .theme-form.login-form h6 {
            color: var(--zhc-muted);

            font-size: 14px;
            font-weight: 400;

            line-height: 1.6;

            margin-bottom: 30px;
        }

        /* =========================================
           LABELS
           ========================================= */

        .theme-form.login-form label {
            color: var(--zhc-text);

            font-size: 14px;
            font-weight: 600;

            margin-bottom: 8px;
        }

        /* =========================================
           INPUT GROUP
           ========================================= */

        .theme-form .input-group {
            border-radius: 7px;

            overflow: hidden;
        }

        .theme-form .input-group-text {
            min-width: 48px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: var(--zhc-blue);

            color: var(--zhc-white);

            border: 1px solid var(--zhc-blue);
        }

        /* =========================================
           INPUT
           ========================================= */

        .theme-form .form-control {
            height: 48px;

            background: var(--zhc-white);

            color: var(--zhc-text);

            border: 1px solid var(--zhc-border);

            box-shadow: none;

            font-size: 14px;
        }

        .theme-form .form-control::placeholder {
            color: #a0aec0;
        }

        .theme-form .form-control:focus {
            border-color: var(--zhc-blue);

            box-shadow: 0 0 0 3px rgba(31, 78, 158, 0.1);
        }

        /* =========================================
           REMEMBER PASSWORD
           ========================================= */

        .theme-form .checkbox input[type="checkbox"] {
            accent-color: var(--zhc-blue);
        }

        .theme-form .checkbox label {
            color: var(--zhc-muted) !important;

            font-weight: 400;
        }

        /* =========================================
           LOGIN BUTTON
           ========================================= */

        .theme-form .btn-primary {
            width: 100%;

            min-height: 49px;

            background: var(--zhc-blue);

            border-color: var(--zhc-blue);

            color: var(--zhc-white);

            border-radius: 7px;

            font-size: 14px;
            font-weight: 600;

            letter-spacing: 0.2px;

            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .theme-form .btn-primary:hover,
        .theme-form .btn-primary:focus {
            background: var(--zhc-blue-dark);

            border-color: var(--zhc-blue-dark);

            color: var(--zhc-white);

            transform: translateY(-1px);

            box-shadow: 0 7px 18px rgba(31, 78, 158, 0.22);
        }

        .theme-form .btn-primary:active {
            transform: translateY(0);
        }

        /* =========================================
           ERROR MESSAGE
           ========================================= */

        .theme-form .alert-danger {
            margin-top: 8px;

            padding: 9px 12px;

            border-radius: 6px;

            font-size: 13px;
        }

        /* =========================================
           PASSWORD SHOW/HIDE
           ========================================= */

        .show-hide {
            background: var(--zhc-white);
        }

        /* =========================================
           MOBILE
           ========================================= */

        @media (max-width: 1199px) {
            .zss-image-side {
                height: 360px;
            }

            .zss-login-area {
                min-height: auto;

                padding: 40px 25px;
            }
        }

        @media (max-width: 767px) {
            .zss-image-side {
                height: 280px;
            }

            .zss-login-area {
                padding: 30px 18px;
            }

            .login-card {
                padding: 35px 25px;
            }
        }

        @media (max-width: 575px) {
            .zss-image-side {
                height: 230px;
            }

            .zss-login-area {
                padding: 25px 15px;
            }

            .login-card {
                padding: 30px 20px;

                border-radius: 9px;
            }

            .theme-form.login-form h4 {
                font-size: 23px;
            }
        }
    </style>
@endpush

<div class="container-fluid">
    <div class="row g-0">
        {{-- ========================================= LEFT / IMAGE
        ========================================= --}}
        <div class="col-xl-5 zss-image-side">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/img/president.jpg') }}" alt="ZSS Login"
                loading="lazy" />
        </div>

        {{-- ========================================= RIGHT / LOGIN
        ========================================= --}}
        <div class="col-xl-7 p-0">
            <div class="zss-login-area">
                <div class="login-card">
                    <form class="theme-form login-form" wire:submit="login">
                        {{-- ========================================= HEADER
                        ========================================= --}}

                        <h4>ZSS Login</h4>

                        <h6>Welcome back! Log in to your account.</h6>

                        {{-- ========================================= EMAIL
                        ========================================= --}}

                        <div class="form-group">
                            <label> Email Address </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="icon-email"></i>
                                </span>

                                <input class="form-control" type="email" wire:model="email"
                                    placeholder="Enter your email address" autocomplete="email" required />
                            </div>
                        </div>

                        @error('email')

                            <div class="alert alert-danger">{{ $message }}</div>

                        @enderror {{-- ========================================= PASSWORD
                        ========================================= --}}

                        <div class="form-group">
                            <label> Password </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="icon-lock"></i>
                                </span>

                                <input class="form-control" type="password" wire:model="password"
                                    placeholder="Enter your password" autocomplete="current-password" required />

                                <div class="show-hide">
                                    <span class="show"></span>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================= REMEMBER ME
                        ========================================= --}}

                        <div class="form-group">
                            <div class="checkbox">
                                <input id="checkbox1" type="checkbox" wire:model="remember" />

                                <label class="text-muted" for="checkbox1">
                                    Remember password
                                </label>
                            </div>
                        </div>

                        {{-- ========================================= LOGIN BUTTON
                        ========================================= --}}

                        <div class="form-group">
                            <button class="btn btn-primary btn-block" type="submit">
                                Sign in
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>