<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index(): View
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|regex:/^08[0-9]{8,12}$/|unique:users,phone,' . $user->id,
            'address' => 'required|string|max:255',
            'rt' => 'required|string|in:01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16,17,18,19,20',
            'rw' => 'required|string|in:01,02,03,04,05,06,07,08,09,10',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_avatar' => 'nullable|boolean',
            // Additional fields
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'occupation' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'rt_number' => 'nullable|string|max:3',
            'rw_number' => 'nullable|string|max:3',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'facebook_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'whatsapp' => 'nullable|string|regex:/^[0-9]{10,15}$/',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.regex' => 'Format nomor HP tidak valid. Gunakan format 08xxxxxxxxxx.',
            'phone.unique' => 'Nomor HP sudah digunakan oleh user lain.',
            'address.required' => 'Alamat wajib diisi.',
            'rt.required' => 'RT wajib dipilih.',
            'rt.in' => 'RT tidak valid.',
            'rw.required' => 'RW wajib dipilih.',
            'rw.in' => 'RW tidak valid.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'bio.max' => 'Bio maksimal 500 karakter.',
            'facebook_url.url' => 'Format URL Facebook tidak valid.',
            'instagram_url.url' => 'Format URL Instagram tidak valid.',
            'twitter_url.url' => 'Format URL Twitter tidak valid.',
            'whatsapp.regex' => 'Format WhatsApp tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Hapus avatar lama jika ada
                if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                    Storage::delete('public/avatars/' . $user->avatar);
                }

                // Upload avatar baru
                $avatarName = time() . '_' . $user->id . '.' . $request->avatar->extension();
                $request->avatar->storeAs('public/avatars', $avatarName);
                $user->avatar = $avatarName;
            }

            // Handle avatar removal
            if ($request->has('remove_avatar') && $request->remove_avatar) {
                if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                    Storage::delete('public/avatars/' . $user->avatar);
                }
                $user->avatar = null;
            }

            // Update profile with all fields
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'avatar' => $user->avatar,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'occupation' => $request->occupation,
                'bio' => $request->bio,
                'rt_number' => $request->rt_number,
                'rw_number' => $request->rw_number,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'facebook_url' => $request->facebook_url,
                'instagram_url' => $request->instagram_url,
                'twitter_url' => $request->twitter_url,
                'whatsapp' => $request->whatsapp,
            ]);

            session()->flash('success', 'Profile berhasil diperbarui!');
            return redirect()->route('profile.index');

        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed' => 'Password harus mengandung huruf besar dan kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.symbols' => 'Password harus mengandung simbol.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak benar.'])
                ->withInput();
        }

        try {
            // Update password
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            session()->flash('success', 'Password berhasil diperbarui!');
            return redirect()->route('profile.index');

        } catch (\Exception $e) {
            \Log::error('Password update failed: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                ->withInput();
        }
    }

    /**
     * Upload user avatar (AJAX).
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Foto profil wajib dipilih.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Hapus avatar lama jika ada
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            // Upload avatar baru
            $avatarName = time() . '_' . $user->id . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);

            // Update database
            $user->update(['avatar' => $avatarName]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui!',
                'avatar_url' => Storage::url('avatars/' . $avatarName)
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Delete user avatar (AJAX).
     */
    public function deleteAvatar(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Hapus file avatar
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            // Update database
            $user->update(['avatar' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil dihapus!',
                'avatar_url' => $user->avatar_url // This will return the default avatar URL
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Get user notifications.
     */
    public function getNotifications(): JsonResponse
    {
        try {
            $user = Auth::user();

            // Implementasi notifikasi sesuai kebutuhan
            $notifications = collect([]); // Placeholder

            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);

        } catch (\Exception $e) {
            \Log::error('Get notifications failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem.'
            ], 500);
        }
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request, $id): JsonResponse
    {
        try {
            // Implementasi mark as read sesuai kebutuhan

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai sebagai dibaca.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Mark notification read failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem.'
            ], 500);
        }
    }

    /**
     * Admin methods - untuk admin mengelola users
     */

    /**
     * Display all users (admin only).
     */
    public function adminIndex(): View
    {
        $users = User::with(['posts', 'reports'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show specific user (admin only).
     */
    public function adminShow(User $user): View
    {
        $user->load(['posts', 'reports']);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Verify user (admin only).
     */
    public function verify(User $user): RedirectResponse
    {
        $user->update(['status' => 'verified']);

        session()->flash('success', 'User berhasil diverifikasi!');
        return redirect()->back();
    }

    /**
     * Block user (admin only).
     */
    public function block(User $user): RedirectResponse
    {
        $user->update(['status' => 'blocked']);

        session()->flash('success', 'User berhasil diblokir!');
        return redirect()->back();
    }

    /**
     * Activate user (admin only).
     */
    public function activate(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        session()->flash('success', 'User berhasil diaktifkan!');
        return redirect()->back();
    }

    /**
     * Delete user (admin only).
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Hapus avatar jika ada
            if ($user->avatar && Storage::exists('public/avatars/' . $user->avatar)) {
                Storage::delete('public/avatars/' . $user->avatar);
            }

            $user->delete();

            session()->flash('success', 'User berhasil dihapus!');
            return redirect()->route('admin.warga.index');

        } catch (\Exception $e) {
            \Log::error('User deletion failed: ' . $e->getMessage());

            session()->flash('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
            return redirect()->back();
        }
    }
}