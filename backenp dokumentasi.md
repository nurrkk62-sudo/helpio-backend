# 🐘 Laravel Backend Integration Guide - HelpIO

Panduan ini disusun khusus untuk tim **Backend Developer (Laravel)** guna mempercepat pembuatan RESTful API yang kompatibel dengan frontend React.js **HelpIO**.

---

## 🛠️ 1. Persiapan Awal & CORS
Pastikan domain frontend React (`http://localhost:5173`) diizinkan untuk melakukan request ke backend Laravel.

### Konfigurasi CORS (Laravel 11+)
Jika menggunakan Laravel 11, terbitkan konfigurasi CORS dengan perintah:
```bash
php artisan config:publish cors
```
Lalu pada `config/cors.php`, sesuaikan setting origin:
```php
'allowed_origins' => ['http://localhost:5173'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => true,
```

---

## 🔒 2. Autentikasi Menggunakan Laravel Sanctum
Frontend mengharapkan token JWT / Bearer token yang dikirim via header `Authorization: Bearer <token>`. Laravel Sanctum sangat direkomendasikan untuk kebutuhan ini karena ringan dan aman.

### Model User & Migration
Pada `database/migrations/..._create_users_table.php`, tambahkan kolom penentu role:
```php
Schema::create('users', function (Blueprint $table) {
    $table->id(); // Primary Key (BigInt / UUID)
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone'); // WA aktif
    $table->string('password');
    $table->enum('role', ['user', 'expert', 'admin'])->default('user');
    $table->text('avatar')->nullable();
    $table->text('address')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

### Response Login Controller
Frontend membutuhkan payload JSON dengan format seperti ini saat sukses login:
```php
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:user,expert,admin'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password) || $user->role !== $request->role) {
        return response()->json([
            'message' => 'Kredensial tidak valid atau Role tidak sesuai.'
        ], 401);
    }

    // Buat token sanctum
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'avatar' => $user->avatar ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
            'address' => $user->address,
        ]
    ], 200);
}
```

---

## 🛣️ 3. Struktur Routing API (`routes/api.php`)

Grup rute API sesuai dengan autentikasi Sanctum dan Middleware Role untuk menjamin keamanan endpoints.

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\VerificationController;

// Public Routes (Tanpa Auth)
Route::prefix('v1')->group(function () {
    Route::post('/auth/register-user', [AuthController::class, 'registerUser']);
    Route::post('/auth/register-expert', [AuthController::class, 'registerExpert']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/experts', [ExpertController::class, 'index']);
    Route::get('/experts/{id}', [ExpertController::class, 'show']);

    // Protected Routes (Butuh Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::patch('/auth/profile', [AuthController::class, 'updateProfile']);
        
        // Orders (Pelanggan & Ahli)
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/user', [OrderController::class, 'userOrders']);
        Route::get('/orders/expert', [OrderController::class, 'expertOrders']);
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        // Reviews
        Route::post('/reviews', [ReviewController::class, 'store']);
        
        // Expert Services
        Route::post('/experts/{expertId}/services', [ExpertController::class, 'addService']);
        Route::delete('/experts/{expertId}/services/{serviceId}', [ExpertController::class, 'deleteService']);

        // Admin Only Routes
        Route::middleware('role:admin')->group(function () {
            Route::get('/admin/verifications', [VerificationController::class, 'index']);
            Route::patch('/admin/verifications/{id}', [VerificationController::class, 'update']);
            Route::delete('/admin/reviews/{id}', [ReviewController::class, 'destroy']);
        });
    });
});
```

---

## 🛡️ 4. Middleware Role Pengguna
Buat middleware baru untuk menyaring akses berdasarkan role pengguna:
```bash
php artisan make:middleware RoleMiddleware
```
Isi kode middleware (`app/Http/Middleware/RoleMiddleware.php`):
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return response()->json([
                'message' => 'Forbidden: Hak akses ditolak.'
            ], 403);
        }

        return $next($request);
    }
}
```
Daftarkan middleware tersebut pada `bootstrap/app.php` (Laravel 11) atau `app/Http/Kernel.php` (Laravel <11).

---

## 📊 5. Format Data JSON yang Diharapkan Frontend
Pastikan respon API untuk model utama mengembalikan kunci dan penamaan yang sesuai dengan data dummy frontend:

### Data Kategori (`/api/v1/categories`)
```json
[
  {
    "id": 1,
    "name": "Service & Cuci AC",
    "icon": "TbAirConditioning",
    "color": "bg-blue-500",
    "subcategories": ["Cuci AC", "Service AC", "Bongkar Pasang AC"]
  }
]
```

### Data Ahli (`/api/v1/experts/{id}`)
```json
{
  "id": "exp_1",
  "name": "Rudi Hermawan",
  "phone": "6281234567891",
  "whatsapp": "6281234567891",
  "verified": true,
  "category": "Service & Cuci AC",
  "location": "Semarang Selatan, Semarang",
  "experience": "8 Tahun",
  "rating": 4.9,
  "completed_jobs": 340,
  "starting_price": 75000,
  "avatar": "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400",
  "bio": "Teknisi AC berpengalaman...",
  "services_list": [
    { "id": "srv_1", "title": "Cuci AC Split", "price": 75000, "estTime": "45 Menit" }
  ]
}
```

### Data Order (`/api/v1/orders/user` & `/api/v1/orders/expert`)
```json
[
  {
    "id": "ORD-9921",
    "userId": 1,
    "userName": "Budi Santoso",
    "expertId": "exp_1",
    "expertName": "Rudi Hermawan",
    "expertCategory": "Service & Cuci AC",
    "serviceTitle": "Cuci AC Split 0.5 - 2 PK",
    "price": 75000,
    "address": "Jl. Pemuda No. 45, Semarang",
    "date": "2026-06-29",
    "time": "10:00",
    "status": "Dalam Proses",
    "paymentMethod": "Cash / Transfer Langsung saat Selesai"
  }
]
```

---

## 📌 Catatan Penting untuk Backend:
1. **Tidak Ada Payment Gateway**: Transaksi 100% COD/Offline. Backend hanya perlu mencatat pesanan, mengubah status order, dan menyimpan riwayat transaksi.
2. **Nomor Handphone**: Simpan nomor telepon dengan kode negara (contoh: `628xxxxxxxx`) agar integrasi tombol chat WhatsApp langsung berfungsi di sisi frontend.
