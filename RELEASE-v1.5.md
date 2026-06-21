# v1.5 — Report Saldo Fix

## 🐛 Bugfix: Laporan Operasional Income-Expense

- **Saldo awal/akhir level top-level tidak sama dengan jumlah grup** — 3 bug di `OpsReportController::buildReportData()`:
  1. Inactive mandor tidak termasuk dalam grup → recordnya tetap masuk ke saldo global tapi tidak muncul di grup manapun
  2. Grup internal tidak tampil jika tidak ada transaksi di periode berjalan (hanya saldo awal)
  3. Grup mandor tidak tampil jika tidak ada transaksi di periode berjalan (hanya saldo awal)
- **Dampak**: `saldo_awal`/`saldo_akhir` top-level ≠ sum seluruh grup; user bingung angka tidak balance
- **Fix**: hapus `->where('is_active', true)` dari query mandor; tambah cek `$saldoAwalIncome > 0 || $saldoAwalExpense > 0` pada kondisi grup

## 🧪 Test Flakiness

- 7 file test (`AbsAdminAttendanceTest`, `AbsenceTest`, `AbsEmployeePayrollTest`, `AbsPayrollTest`, `AbsReportTest`, `OpsDashboardTest`, `OpsEmployeeTest`): pindahkan `User::$skipSubCompanyAutoCreate = true` **sebelum** factory mandor — mencegah observer auto-create sub-company duplikat
- `OpsEmployeeTest`: `toBe(0)` → `toBeFalse()` untuk properti boolean `is_active`
