<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $services = [
                ['name' => 'Wedding Photography', 'has_packages' => true],
                ['name' => 'Wedding Videography', 'has_packages' => true],
                ['name' => 'Portrait Sessions', 'has_packages' => true],
                ['name' => 'Commercial Content', 'has_packages' => false],
                ['name' => 'Event Coverage', 'has_packages' => true],
            ];

            foreach ($services as $service) {
                Service::create($service);
            }
    }
}
