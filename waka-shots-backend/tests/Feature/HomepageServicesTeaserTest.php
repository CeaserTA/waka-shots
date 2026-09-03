<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageServicesTeaserTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_teaser_shows_only_three_services_with_conditional_details(): void
    {
        foreach (range(1, 4) as $number) {
            $service = Service::create([
                'name' => "Service {$number}",
                'description' => "Description {$number}",
                'has_packages' => $number < 3,
                'amount' => $number < 3 ? null : 600000,
                'tagline' => $number === 1 ? 'f/1.8 — Intimate' : null,
                'sort_order' => $number === 4 ? 99 : 4 - $number,
            ]);

            if ($number < 3) {
                Package::create([
                    'service_id' => $service->id,
                    'tier_name' => $number === 1 ? 'Silver' : "Package {$number}",
                    'price' => 1500000,
                ]);

                if ($number === 1) {
                    Package::create([
                        'service_id' => $service->id,
                        'tier_name' => 'Gold',
                        'price' => 2500000,
                    ]);
                }
            }
        }

        $content = $this->get(route('home'))->assertOk()->getContent();

        $this->assertSame(3, substr_count($content, 'spotlight-card bg-black hover:bg-panel'));
        $this->assertStringContainsString('View Packages', $content);
        $this->assertStringContainsString('Learn More', $content);
        $this->assertStringContainsString('Description 1', $content);
        $this->assertStringContainsString('f/1.8 — Intimate', $content);
        $this->assertStringContainsString('Silver · Gold', $content);
        // A service without a tagline shows nothing in its place — the old
        // auto-generated "Waka Shots — 02" filler was removed so the card
        // only ever shows what the admin actually entered.
        $this->assertStringNotContainsString('Waka Shots — 02', $content);
        $this->assertLessThan(strpos($content, 'Description 1'), strpos($content, 'Description 2'));
        $this->assertStringNotContainsString('Service 4', $content);
    }
}