<?php

namespace App\Http\Controllers;

use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FamilyController extends Controller
{
    /**
     * Tampilkan halaman onboarding (UI Vue yang kamu buat).
     */
    public function index()
    {
        // Jika user sudah masuk ke sebuah keluarga, jangan izinkan akses onboarding lagi.
        if (Auth::user()->family_id) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding'); // Pastikan nama file Vue kamu 'Onboarding.vue'
    }

    /**
     * Opsi A: Membuat Ruang Keluarga Baru (Kepala Keluarga).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
        ], [
            'name.required' => 'Nama keluarga harus diisi.',
            'name.min' => 'Nama keluarga minimal 3 karakter.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat record keluarga baru
            // invite_code otomatis di-generate (atau bisa pakai Str::random di sini)
            $family = Family::create([
                'name' => $request->name,
                'invite_code' => $this->generateUniqueInviteCode(),
            ]);

            // 2. Hubungkan user yang login ke keluarga ini
            $request->user()->update([
                'family_id' => $family->id,
                'role' => 'Head', // Set sebagai Kepala Keluarga
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Ruang keluarga "' . $family->name . '" berhasil dibuat!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat membuat keluarga. Silakan coba lagi.');
        }
    }

    /**
     * Opsi B: Bergabung ke Keluarga via Kode Undangan.
     */
    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:families,invite_code',
        ], [
            'invite_code.required' => 'Masukkan kode undangan.',
            'invite_code.exists' => 'Kode undangan tidak valid atau tidak ditemukan.',
        ]);

        $family = Family::where('invite_code', strtoupper($request->invite_code))->firstOrFail();

        // Pastikan user belum terdaftar di keluarga manapun
        $user = $request->user();
        
        $user->update([
            'family_id' => $family->id,
            'role' => 'Member', // Set sebagai Anggota biasa
        ]);

        return redirect()->route('dashboard')->with('success', 'Selamat! Anda telah bergabung dengan keluarga ' . $family->name);
    }

    /**
     * Helper untuk generate invite code yang unik.
     */
    private function generateUniqueInviteCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Family::where('invite_code', $code)->exists());

        return $code;
    }
}