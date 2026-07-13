# HelpIO Backend API Documentation

## GET /api/v1/experts
Mendapatkan daftar ahli dengan dukungan filter.

**Query Parameters:**
* `category_id` (UUID): Filter berdasarkan kategori.
* `location` (String): Filter berdasarkan kota/lokasi.
* `min_rating` (Decimal): Filter rating minimum.
* `verified` (Boolean): Filter status verifikasi (true/false).