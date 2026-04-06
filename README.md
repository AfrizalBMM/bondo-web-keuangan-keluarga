# 👨‍👩‍👧‍👦 Family Finance Hub

Family Finance Hub adalah aplikasi manajemen keuangan keluarga modern yang dirancang untuk membantu pasangan melacak dan mengelola keuangannya secara kolaboratif. Aplikasi ini menawarkan antarmuka yang bersih dan responsif, dilengkapi dengan asisten AI pintar, sinkronisasi data seketika (real-time), serta fitur notifikasi untuk memastikan kondisi finansial keluarga selalu terpantau dengan baik.

## ✨ Fitur Utama

- **🧠 Smart Add AI**: Tambah transaksi semudah *chatting*! Cukup ketik kalimat santai (misal: "Beli token listrik 100 ribu dari dompet BCA"), dan AI akan mengkategorikan dan mencatatnya secara otomatis.
- **⚡ Real-time Synchronization**: Dibangun dengan Laravel Reverb. Saldo dan aktivitas di layar Anda akan langsung diperbarui seketika (real-time) saat pasangan Anda mencatat transaksi.
- **✨ Bondo AI Advisor**: Konsultan keuangan pribadi bertenaga **Groq (Llama 3)**! Chat untuk menganalisa pengeluaran harian, merencanakan pembelian besar (*what-if analysis*), mencari transaksi masa lalu, dan mendapatkan saran berbasis data riil 3 bulan terakhir.
- **🎭 Dual-Stage Loading System**: Pengalaman perpindahan halaman yang premium dengan *progress bar* Royal Blue dan *pulsing logo overlay* transparan untuk memastikan transisi terasa smooth dan modern.
- **👨‍👩‍👧‍👦 Advanced Family Management**: Sistem manajemen keluarga lengkap dengan peran **Kepala Keluarga (Head)** dan **Anggota (Member)**. Kepala keluarga memiliki otoritas penuh untuk mengelola nama grup dan mengeluarkan anggota dari tim.
- **📱 PWA & Push Notifications**: Mendukung fungsionalitas *Progressive Web App* dengan Notifikasi Push yang telah dioptimalkan (VAPID 65-byte standard).
- **👁️ Global Visibility & Theme Toggle**: Akses cepat untuk menyembunyikan saldo (*Masking*) dan beralih antara *Night/Light mode* langsung dari header utama yang responsif.
- **🎨 Premium Bondo Aesthetics**: Identitas visual baru dengan logo kustom "Bondo B", skema warna *Royal Blue* & *Emerald*, serta penggunaan komponen modal berbasis *glassmorphism*.
- **🖼️ Avatar Profil**: Upload dan ganti foto profil langsung dari halaman pengaturan. Foto ditampilkan di navbar dengan fallback inisial nama jika belum ada foto.
- **📊 Modul Finansial Lengkap**: Pusat kendali (*Command Center*) menyeluruh yang mengelola Dompet, Kategori, Aset (Tabungan/Investasi), Target Finansial (Goals), Hutang/Piutang, dan Anggaran Bulanan (Budget).

## 🛠️ Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan *stack* teknologi modern untuk memastikan stabilitas, performa, dan skalabilitas:

- **Backend**: [Laravel 11](https://laravel.com)
- **Frontend Framework**: [Vue.js 3](https://vuejs.org) (Composition API)
- **Routing & SSR**: [Inertia.js](https://inertiajs.com)
- **Styling**: [Tailwind CSS](https://tailwindcss.com) v3
- **WebSockets / Real-time**: [Laravel Reverb](https://reverb.laravel.com)
- **State Management**: [Pinia](https://pinia.vuejs.org) & [@vueuse/core](https://vueuse.org)
- **AI Integration**: [Groq API](https://console.groq.com) (Llama 3.3 70B Versatile) — ultra-fast AI inference
- **Icons**: Lucide Vue Next

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan Family Finance Hub di komputer lokal Anda:

1. **Masuk ke direktori proyek lokal Anda**:
   ```bash
   cd fin-family
   ```

2. **Instal Dependensi Backend (PHP)**:
   ```bash
   composer install
   ```

3. **Instal Dependensi Frontend (Node.js)**:
   ```bash
   npm install
   ```

4. **Persiapkan Environment**:
   Salin *file* konfigurasi bawaan dan hasilkan (*generate*) kunci aplikasi:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Konfigurasi File `.env`**:
   Buka file `.env` dan atur koneksi *database* Anda (misal SQLite atau MySQL).
   Pastikan Anda menambahkan kunci mandiri untuk fitur cerdas AI dan Notifikasi Web Push:
   ```env
   DB_CONNECTION=sqlite # Atau koneksi SQL lainnya
   
   # Konfigurasi Gemini AI
   GEMINI_API_KEY="kunci-api-gemini-anda"
   
   # Konfigurasi PWA Web Push
   VAPID_PUBLIC_KEY="kunci-publik-vapid"
   VAPID_PRIVATE_KEY="kunci-privat-vapid"
   VITE_VAPID_PUBLIC_KEY="${VAPID_PUBLIC_KEY}"
   ```
   *(Catatan: Anda dapat membuat sendiri kunci Web Push VAPID dengan menjalankan `npx web-push generate-vapid-keys` pada terminal Anda).*

6. **Migrasi Database**:
   Jalankan perintah ini untuk membangun tabel-tabel data di dalam sistem:
   ```bash
   php artisan migrate
   ```

7. **Jalankan Aplikasi**:
   Mulai *server* aplikasi dengan membuka beberapa *tab* terminal secara bersamaan:
   ```bash
   # Terminal 1 (Utama - Backend)
   php artisan serve
   
   # Terminal 2 (Aset - Frontend)
   npm run dev
   
   # Terminal 3 (Server Real-time)
   php artisan reverb:start
   ```
   Aplikasi Anda kini sudah daring dan dapat diakses pada `http://localhost:8000`.

## 🤖 Cara Penggunaan Fitur AI Smart Add

Sistem asisten **Smart Add** memungkinkan Anda mencirikan log keuangan tanpa harus berurusan dengan formulir yang rumit:

1. Navigasikan ke panel **Transaksi** dari *sidebar* atau *navbar* utama.
2. Klik tombol sakelar **Smart Add (AI)** yang berada dekat bagian tambah transaksi.
3. Anda akan disambut oleh kolom teks bebas (*chat box*). Ketik aktivitas Anda seolah-olah Anda sedang mengirim pesan singkat.
   *Contoh Input (Prompt):*
   - *"Hari ini aku gajian 5 juta masuk ke dompet BCA."*
   - *"Barusan beli bensin motor 30 ribu pakai uang Tunai."*
   - *"Bayar tagihan listrik PLN bulan ini 250rb dari Mandiri."*
4. Tekan tombol **Kirim** (atau `Enter`). Dalam hitungan detik, AI akan memproses teks tersebut, mencocokkannya secara cerdas dengan daftar Kategori dan Dompet keluarga yang telah Anda persiapkan, lalu menyimpan datanya dengan wujud akuntansi (nominal dan jenis/tipe) yang akurat.
5. **Keajaiban Reverb**: Jika pasangan Anda (*user* lain dari keluarga yang sama) sedang menatap dasbornya di perangkat mereka sendiri, perangkat mereka akan otomatis berkedip dan memperbarui balok saldo tanpa mereka harus menyentuh layar sedikitpun!

---
> Dibangun dengan standar tinggi UI/UX 2026 untuk menginspirasi pencapaian finansial keluarga masa depan.
