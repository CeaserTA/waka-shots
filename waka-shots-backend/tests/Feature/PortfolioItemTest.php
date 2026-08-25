<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_items_can_be_created_with_expected_fields(): void
    {
        $category = Category::create([
            'name' => 'Branding',
            'slug' => 'branding',
        ]);

        $portfolioItem = PortfolioItem::create([
            'category_id' => $category->id,
            'title' => 'Campaign identity',
            'image_path' => '/images/campaign.jpg',
        ]);

        $this->assertDatabaseHas('portfolio_items', [
            'id' => $portfolioItem->id,
            'category_id' => $category->id,
            'title' => 'Campaign identity',
            'image_path' => '/images/campaign.jpg',
        ]);
    }
}
