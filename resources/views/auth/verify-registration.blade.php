<x-guest-layout>
    <style>
        body { margin: 0; }
        .verify-scene { align-items:center; background:#080808; display:flex; min-height:100vh; padding:24px; }
        .verify-card { background:#202020; border:1px solid rgba(255,255,255,.35); border-radius:20px; color:#fff; margin:auto; max-width:520px; padding:34px; width:100%; }
        .verify-card h1 { margin:0 0 10px; text-align:center; }
        .verify-card p { color:#ccc; line-height:1.5; text-align:center; }
        .verify-status { background:rgba(248,131,121,.14); border:1px solid #F88379; border-radius:8px; color:#fff; padding:12px; }
        .verify-error { color:#FFA69E; margin:12px 0; text-align:center; }
        .verify-field { margin-top:18px; }
        .verify-field label { display:block; font-weight:800; margin-bottom:7px; }
        .verify-field input { border:2px solid transparent; border-radius:6px; color:#000; font-size:22px; height:52px; letter-spacing:8px; text-align:center; width:100%; }
        .verify-field input:focus { border-color:#F88379; outline:none; }
        .verify-submit { background:#fff; border:2px solid #111; border-radius:6px; color:#F88379; cursor:pointer; font-size:16px; font-weight:900; height:50px; margin-top:24px; width:100%; }
        .verify-back { color:#fff; display:block; margin-top:18px; text-align:center; }
    </style>

    <main class="verify-scene">
        <section class="verify-card">
            <h1>Verify your registration</h1>
            <p>Enter the six-digit code sent to {{ $pending['email'] }}. It expires after 10 minutes.</p>

            @if(session('status')) <div class="verify-status">{{ session('status') }}</div> @endif
            @if($errors->any()) <div class="verify-error">{{ $errors->first() }}</div> @endif

            <form method="POST" action="{{ route('register.confirm') }}">
                @csrf
                <div class="verify-field">
                    <label for="email_code">Email verification code</label>
                    <input id="email_code" name="email_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus autocomplete="one-time-code">
                </div>
                <button class="verify-submit" type="submit">Verify and create account</button>
            </form>

            <a class="verify-back" href="{{ route('register') }}">Start again</a>
        </section>
    </main>
</x-guest-layout>
