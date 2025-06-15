<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Saving;
use App\Models\Testimonial;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create clients (based on your example data)
        $clients = [
            [
                'name' => 'Rafi',
                'phone' => '83875766344',
                'email' => null,
                'address' => null,
            ],
            [
                'name' => 'Audy',
                'phone' => '85694204203',
                'email' => null,
                'address' => null,
            ],
            [
                'name' => 'Amanda Devia',
                'phone' => '81388043167',
                'email' => 'amanda.devia@email.com',
                'address' => 'Jakarta Selatan',
            ],
        ];

        foreach ($clients as $clientData) {
            $client = Client::create($clientData);

            // Create projects for each client
            $this->createProjectsForClient($client);
        }

        // Create additional dummy clients and projects
        $this->createAdditionalData();
    }

    private function createProjectsForClient(Client $client)
    {
        switch ($client->name) {
            case 'Rafi':
                $project = Project::create([
                    'client_id' => $client->id,
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

                // Create DP payment
                $payment = Payment::create([
                    'project_id' => $project->id,
                    'amount' => 50000,
                    'payment_type' => 'DP',
                    'payment_date' => Carbon::now()->subDays(5),
                    'notes' => 'Down Payment awal proyek',
                    'payment_method' => 'Transfer Bank',
                ]);

                // Create saving record
                Saving::create([
                    'payment_id' => $payment->id,
                    'amount' => 5000, // 10% dari 50000
                    'bank_balance' => 5000,
                    'transaction_date' => $payment->payment_date,
                    'notes' => 'Tabungan 10% dari DP Rafi',
                    'is_verified' => true,
                ]);
                break;

            case 'Audy':
                $project = Project::create([
                    'client_id' => $client->id,
                    'title' => 'Website Penitipan Sewa TK',
                    'description' => 'Pembuatan Website untuk layanan penitipan dan sewa fasilitas Taman Kanak-kanak dengan sistem booking online.',
                    'type' => 'HTML/PHP',
                    'total_value' => 1200000,
                    'dp_amount' => 50000,
                    'paid_amount' => 200000,
                    'status' => 'PROGRESS',
                    'deadline' => Carbon::create(2025, 6, 30),
                    'has_testimonial' => false,
                    'notes' => 'Proyek sudah berjalan 65%, tinggal finishing dan testing',
                ]);

                // Create DP payment
                $dpPayment = Payment::create([
                    'project_id' => $project->id,
                    'amount' => 50000,
                    'payment_type' => 'DP',
                    'payment_date' => Carbon::now()->subDays(20),
                    'notes' => 'Down Payment awal proyek',
                    'payment_method' => 'Transfer Bank',
                ]);

                // Create installment payment
                $installmentPayment = Payment::create([
                    'project_id' => $project->id,
                    'amount' => 150000,
                    'payment_type' => 'INSTALLMENT',
                    'payment_date' => Carbon::now()->subDays(10),
                    'notes' => 'Pembayaran tahap kedua setelah progress 50%',
                    'payment_method' => 'Transfer Bank',
                ]);

                // Create saving records
                Saving::create([
                    'payment_id' => $dpPayment->id,
                    'amount' => 5000,
                    'bank_balance' => 5000,
                    'transaction_date' => $dpPayment->payment_date,
                    'notes' => 'Tabungan 10% dari DP Audy',
                    'is_verified' => true,
                ]);

                Saving::create([
                    'payment_id' => $installmentPayment->id,
                    'amount' => 15000,
                    'bank_balance' => 20000,
                    'transaction_date' => $installmentPayment->payment_date,
                    'notes' => 'Tabungan 10% dari cicilan Audy',
                    'is_verified' => true,
                ]);
                break;

            case 'Amanda Devia':
                $project = Project::create([
                    'client_id' => $client->id,
                    'title' => 'Web FK Collab',
                    'description' => 'Pembuatan website untuk FK Collab dengan sistem manajemen konten, gallery, dan fitur kolaborasi antar member.',
                    'type' => 'LARAVEL',
                    'total_value' => 2000000,
                    'dp_amount' => 600000,
                    'paid_amount' => 2000000,
                    'status' => 'FINISHED',
                    'deadline' => Carbon::create(2025, 3, 31),
                    'has_testimonial' => true,
                    'notes' => 'Proyek selesai tepat waktu, klien sangat puas',
                ]);

                // Create DP payment
                $dpPayment = Payment::create([
                    'project_id' => $project->id,
                    'amount' => 600000,
                    'payment_type' => 'DP',
                    'payment_date' => Carbon::now()->subDays(60),
                    'notes' => 'Down Payment 30%',
                    'payment_method' => 'Transfer Bank',
                ]);

                // Create final payment
                $finalPayment = Payment::create([
                    'project_id' => $project->id,
                    'amount' => 1400000,
                    'payment_type' => 'FINAL',
                    'payment_date' => Carbon::now()->subDays(30),
                    'notes' => 'Pelunasan akhir setelah proyek selesai',
                    'payment_method' => 'Transfer Bank',
                ]);

                // Create saving records
                Saving::create([
                    'payment_id' => $dpPayment->id,
                    'amount' => 60000,
                    'bank_balance' => 80000,
                    'transaction_date' => $dpPayment->payment_date,
                    'notes' => 'Tabungan 10% dari DP Amanda Devia',
                    'is_verified' => true,
                ]);

                Saving::create([
                    'payment_id' => $finalPayment->id,
                    'amount' => 140000,
                    'bank_balance' => 220000,
                    'transaction_date' => $finalPayment->payment_date,
                    'notes' => 'Tabungan 10% dari pelunasan Amanda Devia',
                    'is_verified' => true,
                ]);

                // Create testimonial
                Testimonial::create([
                    'project_id' => $project->id,
                    'content' => 'Sangat puas dengan hasil website yang dibuat! Tim sangat profesional, komunikasi lancar, dan hasilnya melebihi ekspektasi. Website berjalan dengan sangat baik dan fitur-fiturnya sangat membantu untuk kegiatan FK Collab. Highly recommended!',
                    'rating' => 5,
                    'is_published' => true,
                    'client_photo' => null,
                ]);
                break;
        }
    }

    private function createAdditionalData()
    {
        // Create additional clients
        $additionalClients = [
            [
                'name' => 'Budi Santoso',
                'phone' => '82123456789',
                'email' => 'budi.santoso@email.com',
                'address' => 'Bandung, Jawa Barat',
            ],
            [
                'name' => 'Sari Indah',
                'phone' => '81987654321',
                'email' => 'sari.indah@gmail.com',
                'address' => 'Surabaya, Jawa Timur',
            ],
            [
                'name' => 'Rizki Pratama',
                'phone' => '85555444333',
                'email' => null,
                'address' => null,
            ]
        ];

        foreach ($additionalClients as $clientData) {
            $client = Client::create($clientData);

            // Create random projects for additional clients
            $this->createRandomProject($client);
        }
    }

    private function createRandomProject(Client $client)
    {
        $projectTypes = ['HTML/PHP', 'LARAVEL', 'WORDPRESS', 'REACT', 'VUE'];
        $statuses = ['WAITING', 'PROGRESS', 'FINISHED'];

        $projectTitles = [
            'Website Company Profile',
            'Sistem Informasi Akademik',
            'Aplikasi Inventory Management',
            'Website Portfolio',
            'E-Learning Platform',
            'Website Toko Online',
            'Sistem POS',
            'Blog Website',
        ];

        $type = $projectTypes[array_rand($projectTypes)];
        $status = $statuses[array_rand($statuses)];
        $title = $projectTitles[array_rand($projectTitles)];

        // Generate random but realistic values
        $baseValue = rand(800000, 5000000);
        $totalValue = round($baseValue / 100000) * 100000; // Round to nearest 100k
        $dpAmount = round($totalValue * 0.3); // 30% DP

        $project = Project::create([
            'client_id' => $client->id,
            'title' => $title . ' - ' . $client->name,
            'description' => 'Deskripsi proyek ' . $title . ' untuk ' . $client->name . '. Proyek ini mencakup berbagai fitur modern dan responsif.',
            'type' => $type,
            'total_value' => $totalValue,
            'dp_amount' => $dpAmount,
            'paid_amount' => $status === 'FINISHED' ? $totalValue : ($status === 'PROGRESS' ? $dpAmount : ($dpAmount > 0 ? $dpAmount : 0)),
            'status' => $status,
            'deadline' => Carbon::now()->addDays(rand(7, 90)),
            'has_testimonial' => $status === 'FINISHED' ? (rand(0, 1) === 1) : false,
            'notes' => 'Catatan untuk proyek ' . $title,
        ]);

        // Create payments based on status
        if ($project->paid_amount > 0) {
            $payment = Payment::create([
                'project_id' => $project->id,
                'amount' => $project->paid_amount,
                'payment_type' => $status === 'FINISHED' ? 'FULL' : 'DP',
                'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                'notes' => 'Pembayaran untuk proyek ' . $title,
                'payment_method' => 'Transfer Bank',
            ]);

            // Create saving record
            Saving::create([
                'payment_id' => $payment->id,
                'amount' => $project->paid_amount * 0.1,
                'bank_balance' => Saving::sum('amount') + ($project->paid_amount * 0.1),
                'transaction_date' => $payment->payment_date,
                'notes' => 'Tabungan 10% dari ' . $client->name,
                'is_verified' => rand(0, 1) === 1,
            ]);
        }

        // Create testimonial for finished projects
        if ($project->status === 'FINISHED' && $project->has_testimonial) {
            Testimonial::create([
                'project_id' => $project->id,
                'content' => 'Hasil kerja yang memuaskan! Website berjalan dengan lancar dan sesuai dengan kebutuhan kami. Pelayanan sangat profesional dan responsif.',
                'rating' => rand(4, 5),
                'is_published' => rand(0, 1) === 1,
                'client_photo' => null,
            ]);
        }
    }
}
