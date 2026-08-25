<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\RelationManagers\PackageFeaturesRelationManager;
use App\Filament\Resources\Services\RelationManagers\PackagesRelationManager;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\Service;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Packages\PackageResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePackageFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_packages_and_features_can_be_created_with_mass_assignment(): void
    {
        $service = Service::create([
            'name' => 'Weddings',
            'has_packages' => true,
        ]);

        $package = Package::create([
            'service_id' => $service->id,
            'tier_name' => 'Gold',
            'price' => 1500.00,
        ]);

        $feature = PackageFeature::create([
            'package_id' => $package->id,
            'feature_text' => 'Full day coverage',
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Weddings',
            'has_packages' => true,
        ]);

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'service_id' => $service->id,
            'tier_name' => 'Gold',
            'price' => '1500.00',
        ]);

        $this->assertDatabaseHas('package_features', [
            'id' => $feature->id,
            'package_id' => $package->id,
            'feature_text' => 'Full day coverage',
        ]);
    }

    public function test_nested_relation_managers_are_registered_on_service_and_package_resources(): void
    {
        $this->assertContains(PackagesRelationManager::class, ServiceResource::getRelations());
        $this->assertContains(PackageFeaturesRelationManager::class, PackageResource::getRelations());
    }
}
