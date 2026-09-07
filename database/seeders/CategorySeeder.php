<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // PENDAPATAN (INCOME)
            [
                'code' => '4-100',
                'name' => 'Pendapatan Penjualan / Invoice',
                'type' => 'income',
                'is_tax_deductible' => true,
                'description' => 'Pendapatan utama dari penagihan invoice penjualan produk/jasa.',
            ],
            [
                'code' => '4-200',
                'name' => 'Pendapatan Jasa & Layanan Lain',
                'type' => 'income',
                'is_tax_deductible' => true,
                'description' => 'Pendapatan operasional dari layanan tambahan.',
            ],
            [
                'code' => '4-900',
                'name' => 'Pendapatan Bunga Bank & Lain-lain',
                'type' => 'income',
                'is_tax_deductible' => true,
                'description' => 'Pendapatan non-operasional seperti jasa giro dan bunga bank.',
            ],

            // BEBAN OPERASIONAL DEDUCTIBLE (DAPAT DIKURANGKAN SECARA FISKAL)
            [
                'code' => '5-101',
                'name' => 'Beban Gaji, Upah & THR Karyawan',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya gaji, tunjangan, lembur, dan BPJS Ketenagakerjaan/Kesehatan.',
            ],
            [
                'code' => '5-102',
                'name' => 'Beban Sewa Gedung & Kantor',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya sewa ruang kantor, gudang, atau tempat operasional.',
            ],
            [
                'code' => '5-103',
                'name' => 'Beban Listrik, Air, Telepon & Internet',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya utilitas kantor harian.',
            ],
            [
                'code' => '5-104',
                'name' => 'Beban Alat Tulis Kantor (ATK) & Fotokopi',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya perlengkapan cetak, kertas, toner, dan alat tulis.',
            ],
            [
                'code' => '5-105',
                'name' => 'Beban Transportasi, Bensin & Tol',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya perjalanan dinas lokal, BBM armada, dan e-toll.',
            ],
            [
                'code' => '5-106',
                'name' => 'Beban Pemasaran, Promosi & Iklan',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya iklan digital, brosur, expo, dan kegiatan promosi.',
            ],
            [
                'code' => '5-107',
                'name' => 'Beban Pemeliharaan & Perbaikan Sarana',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya servis AC, renovasi ringan kantor, dan perbaikan perangkat.',
            ],
            [
                'code' => '5-108',
                'name' => 'Beban Konsumsi Rapat & Lembur',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya makanan/minuman rapat internal dan lembur karyawan.',
            ],
            [
                'code' => '5-109',
                'name' => 'Beban Jasa Profesional & Notaris/Legal',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya jasa hukum, perizinan usaha, dan konsultan.',
            ],
            [
                'code' => '5-110',
                'name' => 'Beban Logistik & Ekspedisi Pengiriman',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya ongkos kirim barang ke pelanggan atau mitra.',
            ],
            [
                'code' => '5-201',
                'name' => 'Beban Jamuan & Entertainment (Nominatif SPT)',
                'type' => 'expense',
                'is_tax_deductible' => true,
                'description' => 'Biaya jamuan bisnis / entertainment yang dilengkapi Daftar Nominatif resmi DJP.',
            ],

            // BEBAN NON-DEDUCTIBLE (KOREKSI FISKAL POSITIF PADA SPT TAHUNAN)
            [
                'code' => '6-101',
                'name' => 'Beban Keperluan Pribadi Pemilik / Pengurus',
                'type' => 'expense',
                'is_tax_deductible' => false,
                'description' => 'Biaya untuk kepentingan pribadi pemegang saham/pengurus (Koreksi Fiskal Positif).',
            ],
            [
                'code' => '6-102',
                'name' => 'Sanksi Administrasi, Denda & Bunga Pajak',
                'type' => 'expense',
                'is_tax_deductible' => false,
                'description' => 'Denda keterlambatan atau sanksi perpajakan (Tidak boleh dibiayakan fiskal).',
            ],
            [
                'code' => '6-103',
                'name' => 'Biaya Entertainment Tanpa Bukti / Nominatif',
                'type' => 'expense',
                'is_tax_deductible' => false,
                'description' => 'Biaya jamuan yang tidak dibuatkan daftar nominatif menurut aturan DJP.',
            ],
            [
                'code' => '6-104',
                'name' => 'Sumbangan & Donasi Non-Daftar Resmi',
                'type' => 'expense',
                'is_tax_deductible' => false,
                'description' => 'Sumbangan yang tidak memenuhi kriteria PP No. 93/2010.',
            ],
            [
                'code' => '6-105',
                'name' => 'Pengeluaran Tanpa Bukti Sah / Valid',
                'type' => 'expense',
                'is_tax_deductible' => false,
                'description' => 'Pengeluaran tanpa kuitansi atau nota resmi pendukung.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['code' => $cat['code']],
                $cat
            );
        }
    }
}
