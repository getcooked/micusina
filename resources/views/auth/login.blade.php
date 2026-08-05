<x-guest-layout>
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }

        .auth-scene {
            align-items: center;
            background:
                radial-gradient(circle at 28% 46%, rgba(255,33,79,.16), transparent 36%),
                linear-gradient(rgba(0,0,0,.72), rgba(0,0,0,.92)),
                url("{{ asset('food_img/auth-food-combo.png') }}") center / cover no-repeat,
                #020202;
            display: flex;
            min-height: 100vh;
            padding: 24px;
        }

        .auth-frame {
            border: 1px solid rgba(255,255,255,.8);
            border-radius: 26px;
            display: grid;
            gap: 42px;
            grid-template-columns: 1.2fr .8fr;
            margin: 0 auto;
            max-width: 1180px;
            min-height: 620px;
            padding: 44px;
            width: 100%;
        }

        .auth-brand {
            align-items: flex-start;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 520px;
            position: relative;
        }

        .auth-brand::before {
            background: radial-gradient(circle, rgba(255,33,79,.3), transparent 64%);
            content: "";
            height: 440px;
            left: 46%;
            pointer-events: none;
            position: absolute;
            top: 52%;
            transform: translate(-50%, -50%);
            width: 520px;
        }

        .auth-brand-logo {
            height: 116px;
            margin-bottom: 34px;
            object-fit: contain;
            width: 150px;
            z-index: 1;
        }

        .auth-food-hero {
            filter: saturate(1.08) contrast(1.05) drop-shadow(0 28px 34px rgba(0,0,0,.62));
            margin-left: -42px;
            margin-top: -2px;
            -webkit-mask-image: radial-gradient(ellipse at center, #000 58%, rgba(0,0,0,.82) 70%, transparent 88%);
            mask-image: radial-gradient(ellipse at center, #000 58%, rgba(0,0,0,.82) 70%, transparent 88%);
            max-height: 430px;
            max-width: 680px;
            mix-blend-mode: screen;
            object-fit: contain;
            opacity: .95;
            width: min(100%, 660px);
            z-index: 1;
        }

        .auth-card {
            align-self: center;
            background: linear-gradient(135deg, rgba(38,38,38,.94), rgba(15,15,15,.96));
            border-radius: 22px;
            box-shadow: 0 24px 55px rgba(0,0,0,.45);
            color: #fff;
            padding: 36px 34px;
        }

        .auth-card h1 {
            color: #fff;
            font-size: 32px;
            font-weight: 900;
            margin: 0 0 26px;
            text-align: center;
        }

        .auth-field {
            margin-bottom: 18px;
        }

        .auth-field label {
            color: #d9d9d9;
            display: block;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .auth-field input {
            background: #f8f8f8;
            border: 0;
            border-radius: 5px;
            color: #222;
            height: 48px;
            padding: 0 16px;
            width: 100%;
        }

        .auth-helper {
            color: #eee;
            display: block;
            font-size: 12px;
            margin: -4px 0 22px;
            text-align: right;
        }

        .auth-submit {
            background: #F88379;
            border: 0;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            font-weight: 900;
            height: 48px;
            width: 100%;
        }

        .auth-social-title {
            color: #ddd;
            font-size: 13px;
            margin: 24px 0 14px;
            text-align: center;
        }

        .auth-socials {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, 1fr);
        }

        .auth-social {
            align-items: center;
            background: #fff;
            border-radius: 999px;
            color: #111;
            display: flex;
            font-weight: 900;
            height: 42px;
            justify-content: center;
        }

        .auth-social svg {
            display: block;
            height: 20px;
            width: 20px;
        }

        .auth-switch {
            color: #ddd;
            font-size: 13px;
            margin-top: 22px;
            text-align: center;
        }

        .auth-switch a {
            color: #fff;
            font-weight: 900;
        }

        .auth-errors {
            background: rgba(255,33,79,.12);
            border: 1px solid rgba(255,33,79,.55);
            border-radius: 8px;
            color: #fff;
            margin-bottom: 18px;
            padding: 12px;
        }

        @media (max-width: 900px) {
            body { overflow: auto; }
            .auth-frame { grid-template-columns: 1fr; padding: 24px; }
            .auth-brand { align-items: center; min-height: auto; }
            .auth-brand-logo { margin-bottom: 18px; }
            .auth-food-hero { margin: 0; max-height: 320px; }
        }
    </style>

    <main class="auth-scene">
        <section class="auth-frame">
            <div class="auth-brand">
                <img class="auth-brand-logo" src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina">
                <img class="auth-food-hero" src="{{ asset('food_img/auth-food-combo.png') }}" alt="Mi Cusina food combo">
            </div>

            <form class="auth-card" method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf
                <h1>Login</h1>

                @if ($errors->any())
                    <div class="auth-errors">
                        {{ $errors->first() }}
                    </div>
                @endif

                @session('status')
                    <div class="auth-errors">{{ $value }}</div>
                @endsession

                <div class="auth-field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="username@gmail.com" required autofocus autocomplete="off">
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Password" required autocomplete="off">
                </div>

                @if (Route::has('password.request'))
                    <a class="auth-helper" href="{{ route('password.request') }}">Forgot Password?</a>
                @endif

                <button class="auth-submit" type="submit">Sign in</button>

                <div class="auth-social-title">or continue with</div>
                <div class="auth-socials">
                    <span class="auth-social" aria-label="Continue with Google" title="Google">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C4 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.1c-.22-.66-.35-1.36-.35-2.1s.13-1.44.35-2.1V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 4 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                    </span>
                    <span class="auth-social" aria-label="Continue with GitHub" title="GitHub">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#181717" d="M12 .5C5.65.5.99 5.15.99 11.3c0 4.77 3.09 8.82 7.37 10.25.54.1.74-.23.74-.52v-1.82c-3 .64-3.63-1.25-3.63-1.25-.49-1.19-1.19-1.51-1.19-1.51-.97-.64.07-.63.07-.63 1.08.07 1.65 1.07 1.65 1.07.96 1.58 2.51 1.12 3.12.86.1-.67.38-1.12.68-1.38-2.4-.26-4.92-1.16-4.92-5.17 0-1.14.42-2.08 1.1-2.81-.11-.26-.48-1.33.1-2.77 0 0 .9-.28 2.95 1.07.86-.23 1.77-.35 2.68-.35.91 0 1.82.12 2.68.35 2.05-1.35 2.95-1.07 2.95-1.07.58 1.44.21 2.51.1 2.77.69.73 1.1 1.67 1.1 2.81 0 4.02-2.53 4.9-4.94 5.16.39.32.73.96.73 1.94v2.87c0 .28.2.62.75.52 4.28-1.43 7.36-5.48 7.36-10.25C23.01 5.15 18.35.5 12 .5z"/>
                        </svg>
                    </span>
                    <span class="auth-social" aria-label="Continue with Facebook" title="Facebook">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#1877F2" d="M24 12.07C24 5.71 18.63.5 12 .5S0 5.71 0 12.07c0 5.75 4.39 10.52 10.13 11.36v-8.04H7.08v-3.32h3.05V9.54c0-2.88 1.79-4.47 4.54-4.47 1.32 0 2.69.23 2.69.23v2.83h-1.52c-1.49 0-1.96.89-1.96 1.8v2.16h3.34l-.53 3.32h-2.81v8.04C19.61 22.49 24 17.72 24 12.07z"/>
                        </svg>
                    </span>
                </div>

                <div class="auth-switch">
                    Don't have an account yet?
                    <a href="{{ route('register') }}">Register for free</a>
                </div>
            </form>
        </section>
    </main>
</x-guest-layout>
