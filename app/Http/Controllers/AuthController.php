<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm(): View
    {
        // Redirect jika sudah login
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Cek apakah user ada
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return redirect()->back()
                ->withErrors(['email' => 'Email tidak terdaftar.'])
                ->withInput($request->only('email'));
        }

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Update last login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Flash success message
            session()->flash('success', 'Selamat datang di WargaVERSE, ' . $user->name . '!');

            return redirect()->intended(route('home'));
        }

        return redirect()->back()
            ->withErrors(['password' => 'Password yang Anda masukkan salah.'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|regex:/^08[0-9]{8,12}$/|unique:users,phone',
            'address' => 'required|string|max:255',
            'rt' => 'required|string|in:01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20',
            'rw' => 'required|string|in:01,02,03,04,05,06,07,08,09,10',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'terms' => 'required|accepted',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'address.required' => 'Alamat wajib diisi.',
            'rt.required' => 'RT wajib dipilih.',
            'rt.in' => 'RT tidak valid.',
            'rw.required' => 'RW wajib dipilih.',
            'rw.in' => 'RW tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed' => 'Password harus mengandung huruf besar dan kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.symbols' => 'Password harus mengandung simbol.',
            'terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        try {
            // Generate username unik
            $username = $this->generateUniqueUsername($request->name);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'username' => $username,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'password' => Hash::make($request->password),
                'role' => 'warga',
                'status' => 'active', // Langsung aktif tanpa verifikasi
                'registered_at' => now(),
                'registered_ip' => $request->ip(),
            ]);

            session()->flash(
                'success',
                'Pendaftaran berhasil! Anda dapat langsung masuk dengan akun Anda.'
            );

            return redirect()->route('login');

        } catch (\Exception $e) {
            \Log::error('Registration failed: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Update last seen
        if (Auth::check()) {
            Auth::user()->update(['last_seen_at' => now()]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'Anda telah berhasil keluar.');

        return redirect()->route('login');
    }

    /**
     * Generate unique username from name.
     */
    private function generateUniqueUsername(string $name): string
    {
        // Clean name: remove special chars, convert to lowercase
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));

        // Truncate if too long
        if (strlen($cleanName) > 20) {
            $cleanName = substr($cleanName, 0, 20);
        }

        $username = $cleanName;
        $counter = 1;

        // Check if username exists, if yes add number
        while (User::where('username', $username)->exists()) {
            $username = $cleanName . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Check if email exists (AJAX).
     */
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Check if phone exists (AJAX).
     */
    public function checkPhone(Request $request)
    {
        $exists = User::where('phone', $request->phone)->exists();
        return response()->json(['exists' => $exists]);
    }
}