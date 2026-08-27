<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\RelationManagers\PackageFeaturesRelationManager;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Resources\Services\RelationManagers\PackagesRelationManager;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Package;
use App\Models\PackageFeature;
use App\Models\Service;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Packages\PackageResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_service_and_package_creation_uses_list_page_modals(): void
    {
        $this->assertSame(['index', 'edit'], array_keys(ServiceResource::getPages()));
        $this->assertSame(['index', 'edit'], array_keys(PackageResource::getPages()));
        $this->assertSame(ListServices::class, ServiceResource::getPages()['index']->getPage());
        $this->assertSame(ListPackages::class, PackageResource::getPages()['index']->getPage());
    }

    public function test_standalone_service_can_store_an_amount_and_thumbnail(): void
    {
        $service = Service::create([
            'name' => 'Portraits',
            'has_packages' => false,
            'amount' => 750000,
            'thumbnail_path' => 'service-thumbnails/portraits.jpg',
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'amount' => '750000.00',
            'thumbnail_path' => 'service-thumbnails/portraits.jpg',
        ]);
    }

    public function test_package_based_service_clears_a_standalone_amount(): void
    {
        $service = Service::create([
            'name' => 'Weddings',
            'has_packages' => false,
            'amount' => 750000,
        ]);

        $service->update(['has_packages' => true]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'amount' => null,
        ]);
    }

    public function test_public_services_page_renders_standalone_amount_and_r2_thumbnail(): void
    {
        $service = Service::create([
            'name' => 'Portraits',
            'has_packages' => false,
            'amount' => 750000,
            'thumbnail_path' => 'service-thumbnails/portraits.jpg',
        ]);

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('Portraits')
            ->assertSee('UGX 750,000')
            ->assertSee(Storage::disk('r2')->url($service->thumbnail_path));
    }

    public function test_public_services_page_does_not_render_service_amount_for_package_services(): void
    {
        $service = Service::create([
            'name' => 'Weddings',
            'has_packages' => true,
            'amount' => 750000,
        ]);
        Package::create([
            'service_id' => $service->id,
            'tier_name' => 'Gold',
            'price' => 1500.00,
        ]);

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('UGX 1,500')
            ->assertDontSee('UGX 750,000');
    }
}
