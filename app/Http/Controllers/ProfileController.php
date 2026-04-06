<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Family;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Tambahkan ->load('family.users') agar data keluarga & anggotanya ikut dikirim ke Vue
        $request->user()->load('family.users');

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function leaveFamily(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Reset family_id dan role menjadi Member biasa
        $user->update([
            'family_id' => null,
            'role' => 'Member' 
        ]);

        return Redirect::route('dashboard')->with('success', 'Anda telah keluar dari grup keluarga.');
    }

    public function destroyFamily(Request $request, Family $family): RedirectResponse
    {
        // Pastikan hanya Head yang bisa hapus
        if ($request->user()->role !== 'Head' || $request->user()->family_id !== $family->id) {
            abort(403, 'Hanya Kepala Keluarga yang dapat membubarkan keluarga.');
        }

        // PERINGATAN: Cek apakah masih ada anggota keluarga lain (selain Head)
        $memberCount = $family->users()->count();
        if ($memberCount > 1) {
            return Redirect::back()->with('error', 'Tidak dapat membubarkan keluarga karena masih ada anggota lain. Harap minta anggota lain keluar terlebih dahulu.');
        }

        // Hapus data keluarga (Pastikan di database sudah ada 'onDelete cascade' 
        // untuk tabel wallets, transactions, dll agar ikut terhapus)
        $family->delete();

        // Reset user Head sendiri (karena family_id sudah null dari cascade/manual)
        $request->user()->update([
            'family_id' => null,
            'role' => 'Member'
        ]);

        return Redirect::route('dashboard')->with('success', 'Keluarga telah dibubarkan dan data telah dihapus.');
    }

    public function updateFamily(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $family = $request->user()->family;

        if (!$family || $request->user()->role !== 'Head') {
            abort(403, 'Hanya Kepala Keluarga yang dapat mengedit informasi keluarga.');
        }

        $family->update([
            'name' => $request->name
        ]);

        return Redirect::back()->with('success', 'Nama keluarga berhasil diperbarui.');
    }

    public function removeMember(Request $request, User $user): RedirectResponse
    {
        // 1. Pastikan yang menghapus adalah Head
        if ($request->user()->role !== 'Head') {
            abort(403, 'Hanya Kepala Keluarga yang dapat mengeluarkan anggota.');
        }

        // 2. Pastikan member yang dihapus ada di keluarga yang sama
        if ($user->family_id !== $request->user()->family_id) {
            abort(403, 'Anggota tidak ditemukan dalam grup keluarga Anda.');
        }

        // 3. Pastikan tidak menghapus diri sendiri (Head)
        if ($user->id === $request->user()->id) {
            abort(403, 'Anda tidak dapat mengeluarkan diri sendiri.');
        }

        // 4. Reset data keluarga member tersebut
        $user->update([
            'family_id' => null,
            'role' => 'Member'
        ]);

        return Redirect::back()->with('success', 'Anggota ' . $user->name . ' telah dikeluarkan dari grup keluarga.');
    }

}
