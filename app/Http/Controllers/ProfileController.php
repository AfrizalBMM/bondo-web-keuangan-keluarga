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

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Tambahkan ->load('family') agar data keluarga ikut dikirim ke Vue
        $request->user()->load('family');

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

        return Redirect::route('dashboard')->with('message', 'Anda telah keluar dari grup keluarga.');
    }

    public function destroyFamily(Request $request, Family $family): RedirectResponse
    {
        // Pastikan hanya Head yang bisa hapus
        if ($request->user()->role !== 'Head' || $request->user()->family_id !== $family->id) {
            abort(403, 'Hanya Kepala Keluarga yang dapat membubarkan keluarga.');
        }

        // Ambil semua member keluarga ini dan reset mereka sebelum dihapus
        \App\Models\User::where('family_id', $family->id)->update([
            'family_id' => null,
            'role' => 'Member'
        ]);

        // Hapus data keluarga (Pastikan di database sudah ada 'onDelete cascade' 
        // untuk tabel wallets, transactions, dll agar ikut terhapus)
        $family->delete();

        return Redirect::route('dashboard')->with('message', 'Keluarga telah dibubarkan dan data telah dihapus.');
    }

}
