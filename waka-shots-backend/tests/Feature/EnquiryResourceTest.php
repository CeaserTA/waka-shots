<?php

namespace Tests\Feature;

use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnquiryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_enquiry_can_be_created_with_mass_assignment(): void
    {
        $enquiry = Enquiry::create([
            'status' => 'new',
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '555-0100',
            'preferred_date' => '2026-09-01',
            'location' => 'City Hall',
            'budget' => '2500',
            'details' => 'Wedding photography enquiry.',
        ]);

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'status' => 'new',
            'name' => 'Jane Doe',
        ]);
    }

    public function test_enquiry_resource_configuration_is_correct(): void
    {
        Enquiry::create([
            'status' => 'new',
            'name' => 'New Client',
            'email' => 'new-client@example.com',
        ]);

        $this->assertSame('Operations', EnquiryResource::getNavigationGroup());
        $this->assertSame('Enquiries', EnquiryResource::getNavigationLabel());
        $this->assertSame(Enquiry::class, EnquiryResource::getModel());
        $this->assertSame('1', EnquiryResource::getNavigationBadge());
        $this->assertArrayNotHasKey('create', EnquiryResource::getPages());
        $this->assertArrayHasKey('edit', EnquiryResource::getPages());
    }
}
