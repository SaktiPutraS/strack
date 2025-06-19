<?php
// database/seeders/ProjectTypesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectType;

class ProjectTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projectTypes = [
            [
                'name' => 'HTML_PHP',
                'display_name' => 'HTML/PHP',
                'description' => 'Website statis atau dinamis menggunakan HTML dan PHP native',
                'sort_order' => 10,
            ],
            [
                'name' => 'LARAVEL',
                'display_name' => 'Laravel Framework',
                'description' => 'Aplikasi web menggunakan framework Laravel',
                'sort_order' => 20,
            ],
            [
                'name' => 'WORDPRESS',
                'display_name' => 'WordPress',
                'description' => 'Website atau blog menggunakan CMS WordPress',
                'sort_order' => 30,
            ],
            [
                'name' => 'REACT',
                'display_name' => 'React.js',
                'description' => 'Aplikasi web single page menggunakan React.js',
                'sort_order' => 40,
            ],
            [
                'name' => 'VUE',
                'display_name' => 'Vue.js',
                'description' => 'Aplikasi web menggunakan framework Vue.js',
                'sort_order' => 50,
            ],
            [
                'name' => 'FLUTTER',
                'display_name' => 'Flutter',
                'description' => 'Aplikasi mobile cross-platform menggunakan Flutter',
                'sort_order' => 60,
            ],
            [
                'name' => 'MOBILE',
                'display_name' => 'Mobile App',
                'description' => 'Aplikasi mobile native (Android/iOS)',
                'sort_order' => 70,
            ],
            [
                'name' => 'NEXTJS',
                'display_name' => 'Next.js',
                'description' => 'Aplikasi web full-stack menggunakan Next.js',
                'sort_order' => 80,
            ],
            [
                'name' => 'ANGULAR',
                'display_name' => 'Angular',
                'description' => 'Aplikasi web menggunakan framework Angular',
                'sort_order' => 90,
            ],
            [
                'name' => 'OTHER',
                'display_name' => 'Other',
                'description' => 'Teknologi lainnya tidak terdaftar',
                'sort_order' => 1000,
            ],
        ];

        foreach ($projectTypes as $typeData) {
            ProjectType::firstOrCreate(
                ['name' => $typeData['name']],
                $typeData
            );
        }
    }
