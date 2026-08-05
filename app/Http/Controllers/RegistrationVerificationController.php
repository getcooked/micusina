<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RegistrationOtpSender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegistrationVerificationController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function send(Request $request, RegistrationOtpSender $sender): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+639[0-9]{9}$/', 'unique:users,phone'],
            'address' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:15', 'confirmed'],
        ]);

        $emailCode = (string) random_int(100000, 999999);
        if (!$sender->sendEmail($validated['email'], $emailCode)) {
            return back()->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors(['email' => 'The verification email could not be sent. Please try again later.']);
        }

        $request->session()->put('registration_verification', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'password' => Hash::make($validated['password']),
            'email_code' => Hash::make($emailCode),
            'expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        return redirect()->route('register.verify')
            ->with('status', 'We sent a verification code to your email.');
    }

    public function verification(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('registration_verification')) {
            return redirect()->route('register');
        }

        return view('auth.verify-registration', [
            'pending' => $request->session()->get('registration_verification'),
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'email_code' => ['required', 'digits:6'],
        ]);

        $pending = $request->session()->get('registration_verification');

        if (!$pending || now()->timestamp > ($pending['expires_at'] ?? 0)) {
            $request->session()->forget('registration_verification');

            return redirect()->route('register')->withErrors([
                'verification' => 'The verification codes expired. Please register again.',
            ]);
        }

        if (!Hash::check($request->email_code, $pending['email_code'])) {
            return back()->withErrors([
                'verification' => 'The email verification code is incorrect.',
            ]);
        }

        if (User::where('email', $pending['email'])->orWhere('phone', $pending['phone'])->exists()) {
            $request->session()->forget('registration_verification');

            return redirect()->route('register')->withErrors([
                'verification' => 'That email address or phone number is already registered.',
            ]);
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'usertype' => 'user',
            'phone' => $pending['phone'],
            'address' => $pending['address'],
            'password' => $pending['password'],
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        $request->session()->forget('registration_verification');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
