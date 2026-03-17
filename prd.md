# Product Requirement Document (PRD): Family Finance Hub

## 1. Project Overview
Platform keuangan keluarga kolaboratif yang memungkinkan pasangan untuk mencatat, menganalisis, dan merencanakan keuangan dalam satu ekosistem yang sinkron secara *real-time*.

* **Visi:** Menghilangkan hambatan komunikasi finansial antar pasangan melalui transparansi data.
* **Target Pengguna:** Pasangan muda, keluarga kecil, dan individu yang melek teknologi.
* **Tech Stack:** Laravel 11 (PHP 8.2), Inertia.js, Vue.js 3, Tailwind CSS, Google Gemini AI API, PWA.

---

## 2. Onboarding & Authentication
### A. Registrasi & Manajemen Ruang Keluarga
* **Smart Registration:**
    * **Opsi A (Kepala Keluarga):** Mendaftar dan membuat "Ruang Keluarga" baru. Sistem otomatis men-generate **Kode Undangan** unik (e.g., `XDASCASDAS`).
    * **Opsi B (Anggota/Pasangan):** Mendaftar dan memasukkan Kode Undangan pasangan untuk bergabung dalam satu basis data terpusat.
* **User Profile:** Unggah foto profil, edit nama lengkap, alamat email, dan fitur ubah password.

### B. Global Interface Elements
* **Visibility Toggle (Icon Mata):** Fitur masking yang mengubah semua angka saldo sensitif menjadi `****` secara instan di seluruh halaman.
* **Theme Engine:** Toggle antara *Dark Mode* (Slate/Royal Blue) dan *Light Mode*.
* **Header Dropdown:** Akses cepat ke pengaturan akun, manajemen ruang keluarga, dan logout.

---

## 3. Module Specifications

### A. Dashboard (The Command Center)
* **Greeting:** "Selamat [Pagi/Siang/Sore/Malam], [User Name]".
* **Family Info:** Menampilkan Nama Keluarga dan tombol "Salin Kode Undangan".
* **Key Metrics (Bank-Style Cards):**
    * **Total Saldo Tersedia:** Agregat saldo dari seluruh dompet aktif.
    * **Posisi Bersih:** Total Kekayaan (Saldo + Piutang - Hutang).
    * **Cashflow Overview:** Ringkasan Pemasukan dan Pengeluaran bulan berjalan.
* **Visualisasi:**
    * **Pie Chart:** Distribusi pengeluaran berdasarkan kategori.
    * **Mini Trend Chart:** Grafik garis (sparkline) untuk arus kas 7 hari terakhir.
* **Recent Activity:** Tabel 5 transaksi terakhir dengan tombol "Lihat Semua".

### B. Transaksi & Smart Add (Gemini AI)
* **Smart Add (NLP):** Input bar berbasis AI. User cukup mengetik: *"Beli cilok 5rb tunai"* dan Gemini AI otomatis mengisi kategori (Makan), nominal (5.000), dan dompet (Tunai).
* **Manual Input:** Form lengkap mencakup Dompet, Tanggal, Kategori (Pemasukan: Gaji, Bonus, dll; Pengeluaran: Makan, Transport, Tagihan, dll), Jumlah, dan Catatan.
* **History & Management:**
    * Filter canggih berdasarkan rentang tanggal dan sumber dompet.
    * Export data transaksi ke format Excel.
    * Aksi tabel: Edit dan Hapus transaksi dengan konfirmasi.

### C. Dompet (Wallets)
* **Manajemen Akun:** Tambah Dompet dengan Tipe (Bank, E-wallet), Saldo Awal, dan Pilihan Warna Identitas.
* **Output:** Grid daftar dompet dengan visualisasi kartu premium ala aplikasi bank modern.

### D. Kategori (Categories)
* **Customization:** Tambah kategori baru dengan tipe (Pemasukan/Pengeluaran) dan kustomisasi warna untuk grafik.

### E. Asset Management
* **Inventory:** Nama Asset (Emas, Mobil, dll), Nilai Awal, Tanggal Beli, dan % Depresiasi per tahun.
* **Logic:** Sistem menghitung estimasi nilai pasar saat ini (Current Value) secara otomatis berdasarkan rumus depresiasi.

### F. Financial Goals (Savings)
* **Tracking:** Nama Goal, Target Nominal, dan Target Deadline.
* **Visualisasi:** Progress bar gradient dengan info sisa nominal dan persentase pencapaian.
* **Action:** Tombol "Setor ke Goal" untuk mutasi dana dari dompet aktif ke target tabungan.

### G. Hutang & Piutang (Debt Management)
* **Hutang Saya:** Catat pinjaman ke pihak lain, kelola cicilan, dan pilih sumber dompet untuk pembayaran.
* **Piutang Saya:** Monitor uang keluar yang dipinjamkan ke orang lain beserta riwayat pelunasannya.
* **Status:** Info nominal "Telah Dibayar" dan "Sisa Tagihan" yang jelas.

### H. Budgeting & Monitoring
* **Control:** Menetapkan batas maksimal pengeluaran bulanan per kategori.
* **Indicator:** Progress bar yang berubah warna (Hijau ke Merah) sebagai peringatan kesehatan keuangan.

### I. Laporan (Reporting & Analytics)
* **Depth Analysis:** Filter rentang waktu kustom atau filter cepat (7 hari, 30 hari, Bulan ini).
* **Metrics:** Rata-rata pengeluaran harian, selisih kas, dan identifikasi transaksi terbesar.
* **Trend Chart:** Line Chart komprehensif membandingkan Income vs Expense antar bulan.
* **Export:** Generate PDF (Bank Statement Style) dan Excel dengan filter spesifik per dompet.

### J. Sistem Notifikasi & Reminder
* **Push Notifications (Real-time):** Notifikasi instan ke HP pasangan saat ada transaksi baru, perubahan aset, atau pencapaian goals.
* **Smart Debt Alert:** Banner peringatan di Dashboard dan push notification untuk hutang/piutang yang mendekati atau melewati jatuh tempo.
* **Budget Warning:** Notifikasi proaktif saat pengeluaran kategori mencapai 80% dan 100% dari limit.

---

## 4. System Architecture & Technical Design

### A. Core Architecture
* **Full Stack:** Laravel 11 & Inertia.js (Bridge antara Laravel dan Vue).
* **Frontend:** Vue.js 3 dengan Composition API untuk reaktivitas tinggi.
* **Real-time Interaction:** Laravel Reverb atau Pusher untuk sinkronisasi data instan antar perangkat pasangan tanpa refresh.
* **Multi-tenancy:** Arsitektur database berbasis `family_id` untuk isolasi data antar keluarga.

### B. AI Engine Integration
* **Provider:** Google Gemini Pro API.
* **Workflow:** Backend mengirimkan input teks user ke Gemini dengan structured prompt untuk mendapatkan output JSON (nominal, category, wallet_type) yang divalidasi sebelum disimpan.

---

## 5. Technical Constraints & Security

### A. Performance & PWA
* **PWA Ready:** Manifest dan Service Worker untuk instalasi di Android/iOS (Home Screen).
* **UX Performance:** Implementasi **Skeleton Loading (Shimmer Effect)** pada setiap navigasi Inertia untuk menjaga kesan aplikasi native yang cepat.
* **Offline Access:** Caching dasar untuk melihat data terakhir saat tanpa koneksi internet.

### B. Security & Data Integrity
* **Database Integrity:** Wajib menggunakan `DB::transaction()` untuk setiap transaksi yang melibatkan mutasi saldo atau pemindahan dana antar akun.
* **Rate Limiting:** Proteksi pada endpoint API Gemini dan autentikasi.
* **Privacy:** Masking data sensitif di sisi client menggunakan state management Vue saat mode mata aktif.

---

## 6. Professional UI/UX Standards (Bank-Grade)
* **Design System:** Menggunakan sistem grid 8px, typography Inter/Geist Sans, dan palet warna **Royal Blue & Slate**.
* **UI Elements:**
    * **Glassmorphism:** Efek blur pada navbar dan modal box.
    * **Empty States:** Ilustrasi minimalis elegan jika data masih kosong.
    * **Status Colors:** Emerald Green (Success), Rose Red (Danger), Amber Gold (Warning).
* **Security UX:** Notifikasi session timeout dan real-time form validation dengan floating labels.