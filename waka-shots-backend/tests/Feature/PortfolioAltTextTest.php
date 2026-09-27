<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PortfolioAltTextTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create(['name' => 'Weddings', 'slug' => 'weddings']);
    }

    private function item(array $attributes): PortfolioItem
    {
        return PortfolioItem::create($attributes + [
            'category_id' => $this->category->id,
            'image_path' => 'https://cdn.example.test/'.uniqid().'.jpg',
        ]);
    }

    public function test_model_fallbacks(): void
    {
        $described = $this->item(['title' => 'First Dance', 'alt_text' => 'Bride laughing during the first dance']);
        $titled = $this->item(['title' => 'First Dance']);
        $bare = $this->item([]);

        $this->assertSame('Bride laughing during the first dance', $described->displayAlt());
        $this->assertSame('First Dance', $described->displayCaption());

        $this->assertSame('First Dance', $titled->displayAlt());
        $this->assertSame('First Dance', $titled->displayCaption());

        $this->assertSame('Weddings', $bare->displayAlt());
        $this->assertSame('Weddings', $bare->displayCaption());
    }

    public function test_both_pages_use_alt_text_then_title_then_category(): void
    {
        $described = $this->item(['title' => 'First Dance', 'alt_text' => 'Bride laughing during the first dance']);
        $titled = $this->item(['title' => 'Golden Hour Portrait']);
        $bare = $this->item([]);

        foreach ([route('portfolio'), route('home')] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertStringContainsString('src="'.$described->image_path.'" alt="Bride laughing during the first dance"', $html, $url);
            $this->assertStringContainsString('src="'.$titled->image_path.'" alt="Golden Hour Portrait"', $html, $url);
            $this->assertStringContainsString('src="'.$bare->image_path.'" alt="Weddings"', $html, $url);
            $this->assertStringNotContainsString('alt=""', $html, $url);

            // Captions: the title where there is one, the category name where not.
            $this->assertMatchesRegularExpression('#<div class="font-serif text-lg[^"]*">First Dance</div>#', $html, $url);
            $this->assertMatchesRegularExpression('#<div class="font-serif text-lg[^"]*">Golden Hour Portrait</div>#', $html, $url);
            $this->assertMatchesRegularExpression('#<div class="font-serif text-lg[^"]*">Weddings</div>#', $html, $url);
            $this->assertDoesNotMatchRegularExpression('#<div class="font-serif text-lg[^"]*">\s*</div>#', $html, $url);
        }
    }

    public function test_portfolio_page_does_not_query_once_per_item(): void
    {
        $countQueries = function (): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->get(route('portfolio'))->assertOk();

            return count(DB::getQueryLog());
        };

        $this->item([]);
        $countQueries(); // warm-up: site settings are created and memoised on first render
        $withOne = $countQueries();

        foreach (range(1, 5) as $i) {
            $this->item([]);
        }

        $this->assertSame($withOne, $countQueries());
    }
}
