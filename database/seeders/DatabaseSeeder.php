<?php
// database/seeders/DatabaseSeeder.php - Updated with Latest Data June 2025

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Saving;
use App\Models\BankBalance;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createProjectsFromRealData();
        $this->createBankBalanceHistory();
    }

    private function createProjectsFromRealData()
    {
        $projectsData = [
            // WAITING PROJECTS (3 projects)
            ['customer' => 'Rafi', 'testimoni' => false, 'kontak' => '83875766344', 'tipe' => 'HTML/PHP', 'nilai' => 1100000, 'progres' => 'WAITING', 'deadline' => '2025-06-30', 'dp' => 50000, 'pelunasan' => 0, 'piutang' => 1050000, 'keterangan' => 'Website E-Commerce'],
            ['customer' => 'Sipak', 'testimoni' => false, 'kontak' => '85155439091', 'tipe' => 'LARAVEL', 'nilai' => 1500000, 'progres' => 'WAITING', 'deadline' => '2025-06-20', 'dp' => 500000, 'pelunasan' => 0, 'piutang' => 1000000, 'keterangan' => 'Website Pelayanan Appointment RS'],
            ['customer' => 'Wanda', 'testimoni' => false, 'kontak' => '81525959882', 'tipe' => 'HTML/PHP', 'nilai' => 120000, 'progres' => 'WAITING', 'deadline' => '2025-06-22', 'dp' => 50000, 'pelunasan' => 0, 'piutang' => 70000, 'keterangan' => 'Website Layanan TI dan Pengaduan Mahasiswa'],

            // PROGRESS PROJECTS (10 projects)
            ['customer' => 'Dava', 'testimoni' => true, 'kontak' => '85717082754', 'tipe' => 'LARAVEL', 'nilai' => 250000, 'progres' => 'PROGRESS', 'deadline' => '2025-04-15', 'dp' => 0, 'pelunasan' => 250000, 'piutang' => 0, 'keterangan' => 'Revisi Web Kuesioner'],
            ['customer' => 'Audy', 'testimoni' => false, 'kontak' => '85694042303', 'tipe' => 'HTML/PHP', 'nilai' => 1200000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-30', 'dp' => 50000, 'pelunasan' => 950000, 'piutang' => 200000, 'keterangan' => 'Website Penilaian Siswa TK'],
            ['customer' => 'Tan', 'testimoni' => false, 'kontak' => '82123796084', 'tipe' => 'HTML/PHP', 'nilai' => 250000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-07', 'dp' => 0, 'pelunasan' => 250000, 'piutang' => 0, 'keterangan' => 'Website Topup Game'],
            ['customer' => 'Pika', 'testimoni' => false, 'kontak' => '87780850246', 'tipe' => 'LARAVEL', 'nilai' => 1300000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-20', 'dp' => 700000, 'pelunasan' => 0, 'piutang' => 600000, 'keterangan' => 'Figma & Website Sekolah'],
            ['customer' => 'Fira', 'testimoni' => false, 'kontak' => '85890352821', 'tipe' => 'LARAVEL', 'nilai' => 300000, 'progres' => 'PROGRESS', 'deadline' => '2025-05-28', 'dp' => 0, 'pelunasan' => 300000, 'piutang' => 0, 'keterangan' => 'Revisi Website Kampung Kecil'],
            ['customer' => 'Ika', 'testimoni' => false, 'kontak' => '85211857817', 'tipe' => 'OTHER', 'nilai' => 600000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-01', 'dp' => 50000, 'pelunasan' => 550000, 'piutang' => 0, 'keterangan' => 'Figma, Wireframe & Dokumentasi'],
            ['customer' => 'Reza', 'testimoni' => false, 'kontak' => '85783123168', 'tipe' => 'HTML/PHP', 'nilai' => 1000000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-09', 'dp' => 150000, 'pelunasan' => 850000, 'piutang' => 0, 'keterangan' => 'Website Perpustakaan Sekolah'],
            ['customer' => 'Ravelia', 'testimoni' => false, 'kontak' => '81379155009', 'tipe' => 'OTHER', 'nilai' => 475000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-16', 'dp' => 300000, 'pelunasan' => 175000, 'piutang' => 0, 'keterangan' => 'Laporan Bab 4-6'],
            ['customer' => 'Elbiana', 'testimoni' => false, 'kontak' => '85788601992', 'tipe' => 'HTML/PHP', 'nilai' => 1000000, 'progres' => 'PROGRESS', 'deadline' => '2025-06-12', 'dp' => 650000, 'pelunasan' => 350000, 'piutang' => 0, 'keterangan' => 'Website SPP Sekolah'],
            ['customer' => 'Amanda Devia', 'testimoni' => true, 'kontak' => '81388043167', 'tipe' => 'LARAVEL', 'nilai' => 800000, 'progres' => 'PROGRESS', 'deadline' => '2025-05-03', 'dp' => 0, 'pelunasan' => 800000, 'piutang' => 0, 'keterangan' => 'Revisi Web FIK Collab'],

            // FINISHED PROJECTS (20 projects)
            ['customer' => 'Amanda Devia', 'testimoni' => true, 'kontak' => '81388043167', 'tipe' => 'LARAVEL', 'nilai' => 2000000, 'progres' => 'FINISHED', 'deadline' => '2025-03-31', 'dp' => 600000, 'pelunasan' => 1400000, 'piutang' => 0, 'keterangan' => 'Web FIK Collab'],
            ['customer' => 'Suhanda', 'testimoni' => false, 'kontak' => '87881111985', 'tipe' => 'OTHER', 'nilai' => 2200000, 'progres' => 'FINISHED', 'deadline' => '2025-03-13', 'dp' => 0, 'pelunasan' => 2200000, 'piutang' => 0, 'keterangan' => '2 Lisensi (FINA)'],
            ['customer' => 'Dava', 'testimoni' => true, 'kontak' => '85717082754', 'tipe' => 'LARAVEL', 'nilai' => 2000000, 'progres' => 'FINISHED', 'deadline' => '2025-04-05', 'dp' => 2000000, 'pelunasan' => 0, 'piutang' => 0, 'keterangan' => 'Web Kuesioner'],
            ['customer' => 'Dava', 'testimoni' => true, 'kontak' => '85717082754', 'tipe' => 'LARAVEL', 'nilai' => 200000, 'progres' => 'FINISHED', 'deadline' => '2025-04-25', 'dp' => 200000, 'pelunasan' => 0, 'piutang' => 0, 'keterangan' => 'Laporan Web Kuesioner'],
            ['customer' => 'Rio', 'testimoni' => true, 'kontak' => '85828536005', 'tipe' => 'HTML/PHP', 'nilai' => 600000, 'progres' => 'FINISHED', 'deadline' => '2025-04-24', 'dp' => 50000, 'pelunasan' => 550000, 'piutang' => 0, 'keterangan' => 'Web Absensi'],
            ['customer' => 'Wega', 'testimoni' => true, 'kontak' => '81333619280', 'tipe' => 'HTML/PHP', 'nilai' => 1050000, 'progres' => 'FINISHED', 'deadline' => '2025-05-15', 'dp' => 100000, 'pelunasan' => 950000, 'piutang' => 0, 'keterangan' => 'Web Pelayanan SRUT + Hosting'],
            ['customer' => 'Kav', 'testimoni' => true, 'kontak' => '881081000000', 'tipe' => 'LARAVEL', 'nilai' => 210000, 'progres' => 'FINISHED', 'deadline' => '2025-05-25', 'dp' => 10000, 'pelunasan' => 200000, 'piutang' => 0, 'keterangan' => 'Website Data Gizi Buah'],
            ['customer' => 'Ade', 'testimoni' => true, 'kontak' => '85781743145', 'tipe' => 'HTML/PHP', 'nilai' => 400000, 'progres' => 'FINISHED', 'deadline' => '2025-05-27', 'dp' => 150000, 'pelunasan' => 250000, 'piutang' => 0, 'keterangan' => 'Figma dan Website'],
            ['customer' => 'Karin', 'testimoni' => false, 'kontak' => '85141302702', 'tipe' => 'OTHER', 'nilai' => 120000, 'progres' => 'FINISHED', 'deadline' => '2025-06-02', 'dp' => 50000, 'pelunasan' => 70000, 'piutang' => 0, 'keterangan' => 'Prototype & Wireframe Figma'],
            ['customer' => 'Rahma', 'testimoni' => false, 'kontak' => '85736448622', 'tipe' => 'LARAVEL', 'nilai' => 150000, 'progres' => 'FINISHED', 'deadline' => '2025-06-10', 'dp' => 0, 'pelunasan' => 150000, 'piutang' => 0, 'keterangan' => 'Penambahan Fitur Kuis Website'],
            ['customer' => 'Anon1', 'testimoni' => true, 'kontak' => '82276717334', 'tipe' => 'LARAVEL', 'nilai' => 50000, 'progres' => 'FINISHED', 'deadline' => '2025-06-03', 'dp' => 0, 'pelunasan' => 50000, 'piutang' => 0, 'keterangan' => 'Penambahan Fitur Popup Delete'],
            ['customer' => 'Niss', 'testimoni' => true, 'kontak' => '82178775079', 'tipe' => 'LARAVEL', 'nilai' => 150000, 'progres' => 'FINISHED', 'deadline' => '2025-05-31', 'dp' => 0, 'pelunasan' => 150000, 'piutang' => 0, 'keterangan' => 'Form Booking Website'],
            ['customer' => 'byyyyyy', 'testimoni' => true, 'kontak' => '83891105494', 'tipe' => 'OTHER', 'nilai' => 100000, 'progres' => 'FINISHED', 'deadline' => '2025-05-29', 'dp' => 30000, 'pelunasan' => 70000, 'piutang' => 0, 'keterangan' => 'Laporan Pengembangan Aplikasi'],
            ['customer' => 'Clara', 'testimoni' => true, 'kontak' => '8119562230', 'tipe' => 'HTML/PHP', 'nilai' => 100000, 'progres' => 'FINISHED', 'deadline' => '2025-05-20', 'dp' => 30000, 'pelunasan' => 70000, 'piutang' => 0, 'keterangan' => 'Website Kegiatan STARKI'],
            ['customer' => 'Rio', 'testimoni' => true, 'kontak' => '85828536005', 'tipe' => 'HTML/PHP', 'nilai' => 250000, 'progres' => 'FINISHED', 'deadline' => '2025-05-20', 'dp' => 50000, 'pelunasan' => 200000, 'piutang' => 0, 'keterangan' => 'Laporan & Figma Website Absensi'],
            ['customer' => 'Imelda', 'testimoni' => true, 'kontak' => '85896282281', 'tipe' => 'HTML/PHP', 'nilai' => 50000, 'progres' => 'FINISHED', 'deadline' => '2025-05-14', 'dp' => 0, 'pelunasan' => 50000, 'piutang' => 0, 'keterangan' => 'Revisi Codingan'],
            ['customer' => 'Nelis', 'testimoni' => true, 'kontak' => '85868743959', 'tipe' => 'HTML/PHP', 'nilai' => 450000, 'progres' => 'FINISHED', 'deadline' => '2025-05-24', 'dp' => 150000, 'pelunasan' => 300000, 'piutang' => 0, 'keterangan' => 'Web Sekolah'],
            ['customer' => 'Alss', 'testimoni' => true, 'kontak' => '83172900698', 'tipe' => 'LARAVEL', 'nilai' => 400000, 'progres' => 'FINISHED', 'deadline' => '2025-05-14', 'dp' => 200000, 'pelunasan' => 200000, 'piutang' => 0, 'keterangan' => 'Website Perawatan Hewan'],
            ['customer' => 'Anjani', 'testimoni' => true, 'kontak' => '85862611099', 'tipe' => 'HTML/PHP', 'nilai' => 120000, 'progres' => 'FINISHED', 'deadline' => '2025-05-20', 'dp' => 50000, 'pelunasan' => 70000, 'piutang' => 0, 'keterangan' => 'Website Martabak'],
            ['customer' => 'Cici', 'testimoni' => false, 'kontak' => '82130323717', 'tipe' => 'LARAVEL', 'nilai' => 80000, 'progres' => 'FINISHED', 'deadline' => '2025-04-21', 'dp' => 0, 'pelunasan' => 80000, 'piutang' => 0, 'keterangan' => 'Revisi Codingan'],
        ];

        foreach ($projectsData as $data) {
            $this->createProjectFromData($data);
        }
    }

    private function createProjectFromData(array $data)
    {
        // Check if client already exists
        $client = Client::where('phone', $data['kontak'])->first();

        if (!$client) {
            $client = Client::create([
                'name' => $data['customer'],
                'phone' => $data['kontak'],
                'email' => $data['customer'] === 'Amanda Devia' ? 'amanda.devia@email.com' : null,
                'address' => null,
            ]);
        }

        // Calculate actual paid amount
        $totalPaid = $data['dp'] + $data['pelunasan'];

        // Set project dates based on status
        if ($data['progres'] === 'FINISHED') {
            $deadline = Carbon::parse($data['deadline']);
            $createdAt = $deadline->copy()->subDays(rand(20, 60));
            $updatedAt = $deadline->copy()->addDays(rand(1, 7)); // Finished a bit after deadline
        } elseif ($data['progres'] === 'PROGRESS') {
            $deadline = Carbon::parse($data['deadline']);
            $createdAt = $deadline->copy()->subDays(rand(30, 90));
            $updatedAt = Carbon::now()->subDays(rand(1, 15));
        } else { // WAITING
            $deadline = Carbon::parse($data['deadline']);
            $createdAt = Carbon::now()->subDays(rand(5, 30));
            $updatedAt = $createdAt->copy()->addDays(rand(1, 5));
        }

        // Create project
        $project = Project::create([
            'client_id' => $client->id,
            'title' => $data['keterangan'],
            'description' => 'Deskripsi lengkap untuk ' . $data['keterangan'] . '. Proyek ini mencakup berbagai fitur modern dan responsif sesuai kebutuhan klien.',
            'type' => $this->normalizeType($data['tipe']),
            'total_value' => $data['nilai'],
            'dp_amount' => $data['dp'],
            'paid_amount' => $totalPaid,
            'status' => $data['progres'],
            'deadline' => $deadline,
            'has_testimonial' => $data['testimoni'],
            'notes' => 'Project untuk ' . $data['customer'],
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ]);

        // Create payments
        $this->createPaymentsForProject($project, $data, $createdAt, $updatedAt);
    }

    private function createPaymentsForProject(Project $project, array $data, Carbon $createdAt, Carbon $updatedAt)
    {
        $payments = [];

        // Create DP payment if exists
        if ($data['dp'] > 0) {
            $dpDate = $createdAt->copy()->addDays(rand(1, 7));
            $payments[] = [
                'amount' => $data['dp'],
                'type' => 'DP',
                'date' => $dpDate,
                'notes' => 'Down Payment untuk ' . $project->title
            ];
        }

        // Create pelunasan payment if exists
        if ($data['pelunasan'] > 0) {
            $pelunasanDate = $data['progres'] === 'FINISHED'
                ? $updatedAt->copy()->subDays(rand(1, 5))
                : $createdAt->copy()->addDays(rand(10, 30));

            // Determine payment type
            if ($data['dp'] > 0 && $data['piutang'] == 0) {
                $paymentType = 'FINAL'; // There was DP and this completes the payment
            } elseif ($data['dp'] == 0 && $data['piutang'] == 0) {
                $paymentType = 'FULL'; // Single full payment
            } else {
                $paymentType = 'INSTALLMENT'; // Partial payment, still has remaining
            }

            $payments[] = [
                'amount' => $data['pelunasan'],
                'type' => $paymentType,
                'date' => $pelunasanDate,
                'notes' => 'Pelunasan untuk ' . $project->title
            ];
        }

        // Create payment records and savings
        foreach ($payments as $paymentData) {
            $payment = Payment::create([
                'project_id' => $project->id,
                'amount' => $paymentData['amount'],
                'payment_type' => $paymentData['type'],
                'payment_date' => $paymentData['date'],
                'notes' => $paymentData['notes'],
                'payment_method' => 'Transfer Bank',
                'created_at' => $paymentData['date'],
                'updated_at' => $paymentData['date'],
            ]);

            // Create saving record
            $savingStatus = $this->determineSavingStatus($paymentData['date']);
            $saving = Saving::create([
                'payment_id' => $payment->id,
                'amount' => $paymentData['amount'] * 0.1,
                'transaction_date' => $paymentData['date'],
                'status' => $savingStatus,
                'notes' => "Tabungan 10% dari {$project->client->name} - {$project->title}",
                'created_at' => $paymentData['date'],
                'updated_at' => $paymentData['date'],
            ]);

            // Add transfer details if status is TRANSFERRED
            if ($savingStatus === 'TRANSFERRED') {
                $transferDate = $paymentData['date']->copy()->addDays(rand(1, 14));
                $saving->update([
                    'transfer_date' => $transferDate,
                    'transfer_method' => 'Bank Octo',
                    'transfer_reference' => 'TF' . $transferDate->format('Ymd') . rand(1000, 9999),
                ]);
            }
        }
    }

    private function determineSavingStatus(Carbon $paymentDate): string
    {
        // Payments older than 30 days are likely transferred
        // Recent payments (last 30 days) are still pending
        $daysDiff = $paymentDate->diffInDays(Carbon::now());

        if ($daysDiff > 30) {
            return 'TRANSFERRED'; // Older payments likely transferred
        } elseif ($daysDiff > 15) {
            return rand(0, 1) ? 'TRANSFERRED' : 'PENDING'; // 50/50 chance
        } else {
            return 'PENDING'; // Recent payments still pending
        }
    }

    private function createBankBalanceHistory()
    {
        // Get all transferred savings to create realistic bank balance history
        $transferredSavings = Saving::where('status', 'TRANSFERRED')
            ->whereNotNull('transfer_date')
            ->orderBy('transfer_date')
            ->get()
            ->groupBy(function ($saving) {
                return $saving->transfer_date->format('Y-m-d');
            });

        $currentBalance = 0;
        foreach ($transferredSavings as $date => $savings) {
            $dailyTotal = $savings->sum('amount');
            $currentBalance += $dailyTotal;

            BankBalance::create([
                'balance' => $currentBalance,
                'balance_date' => $date,
                'bank_name' => 'Bank Octo',
                'notes' => "Transfer batch {$savings->count()} tabungan - Total: Rp " . number_format($dailyTotal, 0, ',', '.'),
                'is_verified' => true,
                'created_at' => Carbon::parse($date),
                'updated_at' => Carbon::parse($date),
            ]);
        }

        // Add latest bank balance entry (manual update)
        if ($currentBalance > 0) {
            BankBalance::create([
                'balance' => $currentBalance + rand(10000, 50000), // Add some variation
                'balance_date' => Carbon::now()->format('Y-m-d'),
                'bank_name' => 'Bank Octo',
                'notes' => 'Update saldo bank manual - cek saldo terkini',
                'is_verified' => true,
            ]);
        }
    }

    private function normalizeType(string $tipe): string
    {
        $typeMapping = [
            'HTML/PHP' => 'HTML/PHP',
            'PHP' => 'HTML/PHP',
            'HTML' => 'HTML/PHP',
            'LARAVEL' => 'LARAVEL',
            'Laravel' => 'LARAVEL',
            'FIGMA' => 'OTHER',
            'Figma' => 'OTHER',
            'FINA' => 'OTHER',
            'OTHER' => 'OTHER',
            '' => 'OTHER',
        ];

        return $typeMapping[$tipe] ?? 'OTHER';
    }
}
