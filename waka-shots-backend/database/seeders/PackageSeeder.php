<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $packages = [
                'Wedding Photography' => [
                    ['tier_name' => 'Silver', 'price' => '1200.00', 'features' => ['6 hours of coverage', '300 edited photographs', 'Private online gallery']],
                    ['tier_name' => 'Gold', 'price' => '2200.00', 'features' => ['10 hours of coverage', '600 edited photographs', 'Second photographer', 'Engagement session']],
                    ['tier_name' => 'Platinum', 'price' => '3500.00', 'features' => ['Full-day coverage', '800+ edited photographs', 'Two photographers', 'Fine-art album']],
                ],
                'Wedding Videography' => [
                    ['tier_name' => 'Silver', 'price' => '1500.00', 'features' => ['6 hours of coverage', '3-minute highlight film', 'Licensed music']],
                    ['tier_name' => 'Gold', 'price' => '2800.00', 'features' => ['10 hours of coverage', '6-minute highlight film', 'Ceremony edit', 'Drone footage']],
                    ['tier_name' => 'Platinum', 'price' => '4500.00', 'features' => ['Full-day coverage', '10-minute cinematic film', 'Full ceremony and speeches', 'Two videographers']],
                ],
                'Portrait Sessions' => [
                    ['tier_name' => 'Silver', 'price' => '250.00', 'features' => ['45-minute studio session', '1 outfit change', '8 edited images']],
                    ['tier_name' => 'Gold', 'price' => '450.00', 'features' => ['90-minute location session', '3 outfit changes', '20 edited images']],
                    ['tier_name' => 'Platinum', 'price' => '750.00', 'features' => ['Half-day creative session', 'Unlimited outfit changes', '40 edited images', 'Professional prints']],
                ],
                'Event Coverage' => [
                    ['tier_name' => 'Silver', 'price' => '500.00', 'features' => ['3 hours of coverage', '150 edited photographs', 'Online gallery']],
                    ['tier_name' => 'Gold', 'price' => '900.00', 'features' => ['6 hours of coverage', '350 edited photographs', 'Same-day preview gallery']],
                    ['tier_name' => 'Platinum', 'price' => '1400.00', 'features' => ['Full event coverage', '600 edited photographs', 'On-site portrait station', 'Priority delivery']],
                ],
            ];

            foreach ($packages as $serviceName => $servicePackages) {
                $service = Service::where('name', $serviceName)->firstOrFail();

                foreach ($servicePackages as $packageData) {
                    $features = $packageData['features'];
                    unset($packageData['features']);

                    $package = $service->packages()->create($packageData);

                    foreach ($features as $featureText) {
                        $package->packageFeatures()->create(['feature_text' => $featureText]);
                    }
                }
            }
    }
}
