<x-guest-layout>
    <style>
        body {
            margin: 0;
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
            grid-template-columns: 1.1fr .9fr;
            margin: 0 auto;
            max-width: 1180px;
            padding: 38px 44px;
            width: 100%;
        }

        .auth-brand {
            align-items: flex-start;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .auth-brand::before {
            background: radial-gradient(circle, rgba(255,33,79,.3), transparent 64%);
            content: "";
            height: 420px;
            left: 46%;
            pointer-events: none;
            position: absolute;
            top: 52%;
            transform: translate(-50%, -50%);
            width: 500px;
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
            max-height: 410px;
            max-width: 660px;
            mix-blend-mode: screen;
            object-fit: contain;
            opacity: .95;
            width: min(100%, 640px);
            z-index: 1;
        }

        .auth-card {
            align-self: center;
            background: linear-gradient(135deg, rgba(38,38,38,.94), rgba(15,15,15,.96));
            border-radius: 22px;
            box-shadow: 0 24px 55px rgba(0,0,0,.45);
            color: #fff;
            padding: 30px 34px;
        }

        .auth-card h1 {
            color: #fff;
            font-size: 32px;
            font-weight: 900;
            margin: 0 0 20px;
            text-align: center;
        }

        .auth-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, 1fr);
        }

        .auth-field label {
            color: #d9d9d9;
            display: block;
            font-size: 12px;
            margin-bottom: 7px;
        }

        .auth-field input {
            background: #f8f8f8;
            border: 0;
            border-radius: 5px;
            color: #222;
            height: 46px;
            padding: 0 14px;
            width: 100%;
        }

        .auth-full {
            grid-column: 1 / -1;
        }

        .auth-submit {
            background: #F88379;
            border: 0;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            font-weight: 900;
            height: 48px;
            margin-top: 20px;
            width: 100%;
        }

        .auth-switch {
            color: #ddd;
            font-size: 13px;
            margin-top: 18px;
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
            margin-bottom: 16px;
            padding: 12px;
        }

        @media (max-width: 900px) {
            .auth-frame,
            .auth-grid {
                grid-template-columns: 1fr;
            }

            .auth-frame {
                padding: 24px;
            }

            .auth-brand {
                align-items: center;
            }

            .auth-brand-logo {
                margin-bottom: 18px;
            }

            .auth-food-hero {
                margin: 0;
                max-height: 300px;
            }

        }
    </style>

    <main class="auth-scene">
        <section class="auth-frame">
            <div class="auth-brand">
                <img class="auth-brand-logo" src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina">
                <img class="auth-food-hero" src="{{ asset('food_img/auth-food-combo.png') }}" alt="Mi Cusina food combo">
            </div>

            <form class="auth-card" method="POST" action="{{ route('register.send') }}" autocomplete="off">
                @csrf
                <h1>Register</h1>

                @if ($errors->any())
                    <div class="auth-errors">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="auth-grid">
                    <div class="auth-field">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full name" required autofocus autocomplete="off">
                    </div>

                    <div class="auth-field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="username@gmail.com" required autocomplete="off">
                    </div>

                    <div class="auth-field">
                        <label for="phone">Phone</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone', '+639') }}" required autocomplete="off" inputmode="tel" pattern="\+639[0-9]{9}" maxlength="13" oninput="this.value = '+639' + this.value.replace(/[^0-9]/g, '').replace(/^639/, '').slice(0, 9)">
                    </div>

                    <div class="auth-field">
                        <label for="address">Address</label>
                        <input id="address" type="text" name="address" value="{{ old('address') }}" placeholder="Address" required autocomplete="off">
                    </div>

                    <div class="auth-field">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" placeholder="Password" required autocomplete="off" minlength="8" maxlength="15">
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password" required autocomplete="off" minlength="8" maxlength="15">
                    </div>
                </div>

                <button class="auth-submit" type="submit">Send verification codes</button>

                <div class="auth-switch">
                    Already registered?
                    <a href="{{ route('login') }}">Sign in</a>
                </div>
            </form>
        </section>
    </main>
</x-guest-layout>
