# Gudang Planet Backend

Backend API untuk sistem manajemen warehouse dan point of sale (POS) berbasis Laravel.

## 🚀 Fitur Utama

- **Authentication & Authorization** - Sistem login dengan role-based access (SuperAdmin, Owner, Marketing)
- **Inventory Management** - Kelola produk, kategori, unit, dan stok
- **Transaction Management** - Catat pembelian dan penjualan dengan detail
- **Supplier & Customer Management** - Kelola data supplier dan pelanggan
- **Stock Mutations** - Track perubahan stok dengan mutasi inventory
- **Marketing Module** - Kelola marketing dan produk marketing dengan komisi
- **Reporting** - Report komisi marketing dan revenue penjualan
- **API REST** - Semua fitur tersedia via REST API
- **Monitoring & Debugging** - Laravel Telescope terintegrasi (SuperAdmin only)

## 🛠️ Tech Stack

- **Laravel 11** - Web framework
- **MySQL/PostgreSQL** - Database
- **Sanctum** - API authentication
- **Telescope** - Monitoring & debugging
- **Pest** - Testing framework

## ⚙️ Setup

```bash
# Clone repository
git clone <repo-url>

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
```

## 📚 API Endpoints

**Base URL:** `/api/v1`

### Authentication
- `POST /login` - Login
- `POST /logout` - Logout
- `POST /reset-password` - Reset password (authenticated)
- `POST /forgot-password/verify` - Verify username untuk forgot password
- `POST /forgot-password/reset` - Reset password dengan forgot token

### Master Data (Protected Routes)
- `GET|POST /categories` - Kategori produk
- `GET|POST /units` - Unit satuan
- `GET|POST /products` - Data produk
- `GET|POST /suppliers` - Data supplier
- `GET|POST /customers` - Data pelanggan
- `GET|POST /marketings` - Data marketing

### Transactions
- `GET|POST /sales-transactions` - Transaksi penjualan
- `GET|POST /purchase-transactions` - Transaksi pembelian

### Reports
- `GET /reports/marketing-commission` - Report komisi marketing
- `GET /reports/sales-revenue` - Report revenue penjualan

## 🔐 Monitoring

Akses Telescope di `/telescope-admin/login` (SuperAdmin only)

## 📝 License

MIT License
