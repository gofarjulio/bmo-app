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

## Install ke HP

- **Android:** Menu (⋮) → Add to Home Screen
- **iPhone:** Share → Add to Home Screen
