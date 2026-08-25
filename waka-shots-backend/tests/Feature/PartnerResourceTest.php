<?php

namespace Tests\Feature;

use App\Filament\Resources\Partners\PartnerResource;
use App\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartnerResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_can_be_created_with_mass_assignment(): void
    {
        $partner = Partner::create([
            'name' => 'Acme Studio',
            'logo_path' => 'partners/acme-logo.png',
            'website_url' => 'https://acme.example',
            'sort_order' => 2,
        ]);

        $this->assertDatabaseHas('partners', [
            'id' => $partner->id,
            'name' => 'Acme Studio',
            'logo_path' => 'partners/acme-logo.png',
            'website_url' => 'https://acme.example',
            'sort_order' => 2,
        ]);
    }

    public function test_partner_resource_configuration_is_correct(): void
    {
        $this->assertSame('Content', PartnerResource::getNavigationGroup());
        $this->assertSame('Partners', PartnerResource::getNavigationLabel());
        $this->assertSame(Partner::class, PartnerResource::getModel());
    }
}
