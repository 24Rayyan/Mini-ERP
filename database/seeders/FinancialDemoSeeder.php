<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Document;
use App\Models\EntertainmentDetail;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancialDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catSales = Category::where('code', '4-100')->first();
        $catService = Category::where('code', '4-200')->first();
        $catSalary = Category::where('code', '5-101')->first();
        $catRent = Category::where('code', '5-102')->first();
        $catUtility = Category::where('code', '5-103')->first();
        $catStationery = Category::where('code', '5-104')->first();
        $catTransport = Category::where('code', '5-105')->first();
        $catMarketing = Category::where('code', '5-106')->first();
        $catEntertainment = Category::where('code', '5-201')->first();
        $catPersonal = Category::where('code', '6-101')->first();
        $catTaxPenalty = Category::where('code', '6-102')->first();

        // 1. Pemasukan (Income)
        if ($catSales) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-IN/2026/08/0001'],
                [
                    'type' => 'income',
                    'category_id' => $catSales->id,
                    'amount' => 150000000,
                    'transaction_date' => '2026-08-10',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Pelunasan Pengadaan Server & Rackmount - PT Telkom Akses',
                ]
            );

            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-IN/2026/09/0001'],
                [
                    'type' => 'income',
                    'category_id' => $catSales->id,
                    'amount' => 85000000,
                    'transaction_date' => '2026-09-01',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Pelunasan Maintenance Tahunan IT Infrastructure - Bank Mandiri',
                ]
            );
        }

        // 2. Beban Operasional Fiskal (Deductible)
        if ($catSalary) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0001'],
                [
                    'type' => 'expense',
                    'category_id' => $catSalary->id,
                    'amount' => 45000000,
                    'transaction_date' => '2026-08-25',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Gaji Pokok & Tunjangan Karyawan Periode Agustus 2026',
                ]
            );
        }

        if ($catRent) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0002'],
                [
                    'type' => 'expense',
                    'category_id' => $catRent->id,
                    'amount' => 15000000,
                    'transaction_date' => '2026-08-01',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Sewa Ruang Kantor Bulan Agustus 2026',
                ]
            );
        }

        if ($catUtility) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0003'],
                [
                    'type' => 'expense',
                    'category_id' => $catUtility->id,
                    'amount' => 4200000,
                    'transaction_date' => '2026-08-15',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Tagihan Listrik PLN & Internet Dedicated Fiber',
                ]
            );
        }

        if ($catStationery) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0004'],
                [
                    'type' => 'expense',
                    'category_id' => $catStationery->id,
                    'amount' => 1850000,
                    'transaction_date' => '2026-08-18',
                    'payment_method' => 'Cash',
                    'description' => 'Pembelian Kertas A4 Rim & Toner Laserjet Kantor',
                ]
            );
        }

        // 3. Beban Entertainment (Dengan Lampiran Nominatif SPT)
        if ($catEntertainment) {
            $trxEnt1 = Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0005'],
                [
                    'type' => 'expense',
                    'category_id' => $catEntertainment->id,
                    'amount' => 3500000,
                    'transaction_date' => '2026-08-12',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Jamuan Makan Malam & Diskusi Kerjasama Proyek Cloud DCI',
                ]
            );

            EntertainmentDetail::firstOrCreate(
                ['transaction_id' => $trxEnt1->id],
                [
                    'event_date' => '2026-08-12',
                    'location' => 'Restoran Plataran Menteng, Jakarta Pusat',
                    'attendee_name' => 'Bambang Sudibyo, S.T.',
                    'attendee_company' => 'PT Telekomunikasi Indonesia Tbk',
                    'attendee_position' => 'Senior Procurement Manager',
                    'purpose' => 'Pembahasan Rencana Tender Proyek Jaringan Data Q4 2026',
                ]
            );

            $trxEnt2 = Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0006'],
                [
                    'type' => 'expense',
                    'category_id' => $catEntertainment->id,
                    'amount' => 2750000,
                    'transaction_date' => '2026-08-20',
                    'payment_method' => 'Cash',
                    'description' => 'Lunch Meeting Evaluasi Layanan IT Support SLA',
                ]
            );

            EntertainmentDetail::firstOrCreate(
                ['transaction_id' => $trxEnt2->id],
                [
                    'event_date' => '2026-08-20',
                    'location' => 'Hotel Fairmont, Senayan, Jakarta',
                    'attendee_name' => 'Hendrawan Pratama',
                    'attendee_company' => 'PT Bank Mandiri (Persero) Tbk',
                    'attendee_position' => 'Head of Infrastructure Division',
                    'purpose' => 'Review Kinerja Perangkat Server & Pembahasan Perpanjangan Kontrak',
                ]
            );
        }

        // 4. Beban Non-Deductible (Koreksi Fiskal Positif SPT)
        if ($catTaxPenalty) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0007'],
                [
                    'type' => 'expense',
                    'category_id' => $catTaxPenalty->id,
                    'amount' => 1200000,
                    'transaction_date' => '2026-08-28',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Sanksi Administrasi Keterlambatan Pelaporan Pajak Masa',
                ]
            );
        }

        if ($catPersonal) {
            Transaction::firstOrCreate(
                ['transaction_number' => 'TRX-OUT/2026/08/0008'],
                [
                    'type' => 'expense',
                    'category_id' => $catPersonal->id,
                    'amount' => 2500000,
                    'transaction_date' => '2026-08-30',
                    'payment_method' => 'Bank Transfer',
                    'description' => 'Pengeluaran Pribadi Pengurus (Koreksi Fiskal Positif)',
                ]
            );
        }
    }
}
