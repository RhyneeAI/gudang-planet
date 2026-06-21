# v1.4 — Absence Module + Operational Enhancement

## 🆕 Modul Absensi (Baru)

- **Absensi**: check-in/out dengan GPS geofencing + validasi shift
- **Payroll**: generate otomatis per periode, slip PDF
- **Bonus & Potongan**: kelola bonus dan potongan per karyawan
- **Dashboard**: grafik kehadiran, ringkasan per shift/cabang
- **Shift & Jabatan**: CRUD dengan ordering custom
- **Laporan**: attendance, payroll, deductions, bonuses, employees — support export XLSX

## 🔧 Operasional — Perbaikan & Refactor

- **Flat routes** — route ops tanpa prefix `/admin/`/`/mandor/` (konsisten dengan POS)
- **Multi-file proofs** — `proof_files[]` array (admin) & `mandor_proof_files[]` (mandor) — backward compat dihapus total
- **SPLIT_BILL** — payment method baru enum + migration support MySQL & PostgreSQL
- **Proof directories** — dipisah per role: `operational/proofs/{admin,mandor}/{incomes,expenses,transfers}/`
- **Laporan income-expense** — report endpoint + download PDF/Excel per cabang/mandor

## 🚀 Export System — Cache & Queue (Redis)

- **Cache** — hash-based dedup: request identik langsung return file tanpa regenerate
- **Queue** — export via Redis background worker (`GenerateReportExport` job + `ExportToken` tracker)
- **Fallback** — otomatis sync jika Redis tidak tersedia
- **Endpoint status** — `GET /api/v1/exports/{token}` untuk polling export async
- **Storage paths English** — `reports/{absence,operational,pos}/{subfolder}/`

## 🧹 Lain-lain

- `FileHelper` utility terpusat (save, excel, URL, delete)
- `CleanOldReports` command — hapus otomatis file report lama
- `robots.txt` — blok crawler
- `redis-queue-setup-guide.md` — panduan setup Redis di shared hosting
- Postman collection Ops API
