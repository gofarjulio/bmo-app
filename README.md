# Battery Manufacturing Optimization

Aplikasi web lengkap untuk optimasi produksi baterai INCOE.

## Menu Aplikasi

| Menu | Fungsi |
|------|--------|
| **Cycle Time** | Data CT 23 proses di 7 line assembling |
| **Capacity** | Production Capacity Standard (PCS) per battery type |
| **Loading vs Capacity** | Import order, bandingkan dengan kapasitas, chart stacked + utilization |
| **Man Power Calculator** | Hitung kebutuhan operator per line berdasarkan order & CT |

## Formula

- BATT/H = 3600 ÷ CT(s)
- S1 = 435 × 60 ÷ CT(s) | S2 = 405 × 60 ÷ CT(s) | S3 = 370 × 60 ÷ CT(s)
- Man Power = CEIL(Qty × CT) ÷ (Menit × 60 × Efisiensi × Shift)

## Cara Akses

`https://<username>.github.io/bmo-app`

## Sinkron Data Antar Device (LAN Kantor)

Mulai versi ini, aplikasi bisa sinkron ke server via endpoint:

- `GET /api/state.php`
- `POST /api/state.php`

### Kebutuhan server

- Web server yang mendukung PHP (Apache + PHP / IIS + PHP / Nginx + PHP-FPM).
- Folder `data/` harus bisa ditulis oleh web server (untuk `data/state.json`).

### Cara pakai

1. Deploy folder project ke server (misal `http://10.19.16.25/bmo-app/`).
2. Pastikan `http://10.19.16.25/bmo-app/api/state.php` bisa diakses.
3. Buka dari device A dan upload data.
4. Buka dari device B (URL sama) lalu refresh.
5. Status kiri bawah akan menampilkan:
   - `Storage: Server Sync` = data tersimpan terpusat.
   - `Storage: Local (...)` = fallback localStorage (server sync belum aktif).

## Install ke HP

- **Android:** Menu (⋮) → Add to Home Screen
- **iPhone:** Share → Add to Home Screen
