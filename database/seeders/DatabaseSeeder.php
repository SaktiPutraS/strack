<?php
// database/seeders/DatabaseSeeder.php - Updated for Manual Transfer System

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
        // Create all clients and projects based on real data
        $this->createRealProjectData();

        // Simulate some transfers to show realistic data
        $this->simulateTransfers();
    }

    private function createRealProjectData()
    {
        // WAITING Projects
        $this->createWaitingProjects();

        // PROGRESS Projects
        $this->createProgressProjects();

        // FINISHED Projects
        $this->createFinishedProjects();
    }

    private function createWaitingProjects()
    {
        // Rafi - Website E-Commerce
        $rafi = Client::create([
            'name' => 'Rafi',
            'phone' => '83875766344',
            'email' => null,
            'address' => null,
        ]);

        $rafiProject = Project::create([
            'client_id' => $rafi->id,
            'title' => 'Website E-Commerce',
            'description' => 'Pembuatan Website E-Commerce dengan fitur katalog produk, keranjang belanja, dan sistem pembayaran online.',
            'type' => 'HTML/PHP',
            'total_value' => 1100000,
            'dp_amount' => 50000,
            'paid_amount' => 50000,
            'status' => 'WAITING',
            'deadline' => Carbon::create(2025, 6, 30),
            'has_testimonial' => false,
            'notes' => 'Klien meminta desain modern dengan tema warna biru',
        ]);

        $this->createPaymentAndSaving($rafiProject, 50000, 'DP', Carbon::now()->subDays(5), 'PENDING');

        // Sipak - Website Pelayanan Appointment RS
        $sipak = Client::create([
            'name' => 'Sipak',
            'phone' => '85155439091',
            'email' => null,
            'address' => null,
        ]);

        $sipakProject = Project::create([
            'client_id' => $sipak->id,
            'title' => 'Website Pelayanan Appointment RS',
            'description' => 'Sistem appointment rumah sakit dengan fitur booking jadwal dokter, notifikasi, dan manajemen antrian.',
            'type' => 'LARAVEL',
            'total_value' => 1500000,
            'dp_amount' => 500000,
            'paid_amount' => 500000,
            'status' => 'WAITING',
            'deadline' => Carbon::create(2025, 6, 20),
            'has_testimonial' => false,
            'notes' => 'Sistem appointment untuk rumah sakit',
        ]);

        $this->createPaymentAndSaving($sipakProject, 500000, 'DP', Carbon::now()->subDays(7), 'PENDING');
    }

    private function createProgressProjects()
    {
        // Create some progress projects with mixed PENDING and TRANSFERRED savings
        $progressData = [
            ['name' => 'Audy', 'phone' => '85694042303', 'title' => 'Website Penilaian Siswa TK', 'type' => 'HTML/PHP', 'value' => 1200000, 'paid' => 200000],
            ['name' => 'Tan', 'phone' => '82123796084', 'title' => 'Website Topup Game', 'type' => 'HTML/PHP', 'value' => 250000, 'paid' => 250000],
            ['name' => 'Pika', 'phone' => '87780850246', 'title' => 'Figma & Website Sekolah', 'type' => 'LARAVEL', 'value' => 1300000, 'paid' => 700000],
            ['name' => 'Ika', 'phone' => '85211857817', 'title' => 'Figma, Wireframe & Dokumentasi', 'type' => 'OTHER', 'value' => 600000, 'paid' => 600000],
        ];

        foreach ($progressData as $data) {
            $client = Client::create(['name' => $data['name'], 'phone' => $data['phone'], 'email' => null, 'address' => null]);
            $project = Project::create([
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => 'Deskripsi untuk ' . $data['title'],
                'type' => $data['type'],
                'total_value' => $data['value'],
                'paid_amount' => $data['paid'],
                'status' => 'PROGRESS',
                'deadline' => Carbon::now()->addDays(rand(10, 60)),
                'has_testimonial' => false,
            ]);

            // Create payment with PENDING savings (recent payments)
            $this->createPaymentAndSaving($project, $data['paid'], 'DP', Carbon::now()->subDays(rand(1, 10)), 'PENDING');
        }
    }

    private function createFinishedProjects()
    {
        // Create finished projects with TRANSFERRED savings (older payments)
        $finishedData = [
            ['name' => 'Amanda Devia', 'phone' => '81388043167', 'email' => 'amanda.devia@email.com', 'title' => 'Web FIK Collab', 'type' => 'LARAVEL', 'value' => 2000000, 'testimonial' => true],
            ['name' => 'Dava', 'phone' => '85717082754', 'title' => 'Web Kuesioner', 'type' => 'LARAVEL', 'value' => 2000000, 'testimonial' => true],
            ['name' => 'Rio', 'phone' => '85828536005', 'title' => 'Web Absensi', 'type' => 'HTML/PHP', 'value' => 600000, 'testimonial' => true],
            ['name' => 'Wega', 'phone' => '81333619280', 'title' => 'Web Pelayanan SRUT + Hosting', 'type' => 'HTML/PHP', 'value' => 1050000, 'testimonial' => true],
            ['name' => 'Kav', 'phone' => '881081000000', 'title' => 'Website Data Gizi Buah', 'type' => 'LARAVEL', 'value' => 210000, 'testimonial' => true],
            ['name' => 'Ade', 'phone' => '85781743145', 'title' => 'Figma dan Website', 'type' => 'HTML/PHP', 'value' => 400000, 'testimonial' => true],
            ['name' => 'Nelis', 'phone' => '85868743959', 'title' => 'Web Sekolah', 'type' => 'HTML/PHP', 'value' => 450000, 'testimonial' => true],
            ['name' => 'Alss', 'phone' => '83172900698', 'title' => 'Website Perawatan Hewan', 'type' => 'LARAVEL', 'value' => 400000, 'testimonial' => true],
        ];

        foreach ($finishedData as $data) {
            $client = Client::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => null,
            ]);

            $finishedDate = Carbon::now()->subDays(rand(15, 60));
            $project = Project::create([
                'client_id' => $client->id,
                'title' => $data['title'],
                'description' => 'Deskripsi untuk ' . $data['title'],
                'type' => $data['type'],
                'total_value' => $data['value'],
                'paid_amount' => $data['value'],
                'status' => 'FINISHED',
                'deadline' => $finishedDate->copy()->subDays(rand(7, 30)),
                'has_testimonial' => $data['testimonial'],
                'updated_at' => $finishedDate,
                'created_at' => $finishedDate->copy()->subDays(rand(15, 45)),
            ]);

            // Create payment with TRANSFERRED savings (older payments that have been transferred)
            $this->createPaymentAndSaving($project, $data['value'], 'FULL', $finishedDate->copy()->subDays(rand(1, 5)), 'TRANSFERRED', $finishedDate->copy()->addDays(rand(1, 7)));
        }
    }

    private function simulateTransfers()
    {
        // Create some bank balance records to simulate transfer history
        $transferDates = [
            Carbon::now()->subDays(45),
            Carbon::now()->subDays(30),
            Carbon::now()->subDays(15),
        ];

        $currentBalance = 0;
        foreach ($transferDates as $index => $date) {
            // Get some transferred savings for this period
            $transferredSavings = Saving::where('status', 'TRANSFERRED')
                ->where('transfer_date', '<=', $date)
                ->sum('amount');

            if ($transferredSavings > 0) {
                $currentBalance = $transferredSavings;
                BankBalance::create([
                    'balance' => $currentBalance,
                    'balance_date' => $date->toDateString(),
                    'bank_name' => 'Bank Octo',
                    'notes' => "Transfer batch ke-" . ($index + 1) . " - Total: Rp " . number_format($currentBalance, 0, ',', '.'),
                    'is_verified' => true,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }

    private function createPaymentAndSaving(Project $project, float $amount, string $paymentType, Carbon $paymentDate, string $savingStatus = 'PENDING', ?Carbon $transferDate = null)
    {
        $payment = Payment::create([
            'project_id' => $project->id,
            'amount' => $amount,
            'payment_type' => $paymentType,
            'payment_date' => $paymentDate,
            'notes' => "Pembayaran {$paymentType} untuk {$project->title}",
            'payment_method' => 'Transfer Bank',
        ]);

        // Create saving record (10%) with specified status
        $savingData = [
            'payment_id' => $payment->id,
            'amount' => $amount * 0.1,
            'transaction_date' => $paymentDate,
            'status' => $savingStatus,
            'notes' => "Tabungan 10% dari {$project->client->name} - {$project->title}",
        ];

        // Add transfer details if status is TRANSFERRED
        if ($savingStatus === 'TRANSFERRED' && $transferDate) {
            $savingData['transfer_date'] = $transferDate;
            $savingData['transfer_method'] = 'Bank Octo';
            $savingData['transfer_reference'] = 'TF' . $transferDate->format('Ymd') . rand(1000, 9999);
        }

        Saving::create($savingData);
    }
}
