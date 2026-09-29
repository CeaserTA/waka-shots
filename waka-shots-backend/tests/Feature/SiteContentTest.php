<?php

namespace Tests\Feature;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// One request per test: the view composer memoises SiteSetting with once(),
// which persists across requests inside a single test.
class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    private function setContent(array $content, array $attributes = []): void
    {
        SiteSetting::current()->update(['content' => $content] + $attributes);
    }

    public function test_blank_content_falls_back_to_defaults(): void
    {
        $setting = new SiteSetting(['content' => [
            'home' => ['hero_eyebrow' => '', 'hero_words' => ['', null]],
            'stats' => [['value' => '', 'label' => null]],
        ]]);

        $this->assertSame('Waka Shots Photography — Kampala, Uganda', $setting->content('home.hero_eyebrow'));
        $this->assertSame(SiteSetting::contentDefault('home.hero_words'), $setting->content('home.hero_words'));
        $this->assertSame(SiteSetting::contentDefault('stats'), $setting->content('stats'));
    }

    public function test_paragraphs_split_on_blank_lines(): void
    {
        $this->assertSame(['One.', 'Two.'], SiteSetting::paragraphs("One.\n\n  \nTwo.\n"));
        $this->assertSame([], SiteSetting::paragraphs(null));
    }

    public function test_about_page_renders_defaults(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Patience over performance')
            ->assertSee('Years Behind the Lens')
            ->assertSee('https://images.unsplash.com/photo-1567531708788-4c44105d00ff', false);
    }

    public function test_about_page_uses_admin_content(): void
    {
        $this->setContent([
            'about' => [
                'hero_image' => 'site-settings/about.jpg',
                'hero_heading' => 'Meet the Studio',
                'values' => [['title' => 'Show up early', 'description' => 'Always.']],
            ],
            'stats' => [['value' => '300+', 'label' => 'Weddings']],
        ], ['photographer_bio' => "First paragraph.\n\nSecond paragraph."]);

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('https://cdn.r2.test/site-settings/about.jpg', false)
            ->assertSee('Meet the Studio')
            ->assertSee('Show up early')
            ->assertDontSee('Patience over performance')
            ->assertSee('300+')
            ->assertDontSee('Years Behind the Lens')
            ->assertSee('<p class="text-ivory-dim font-light max-w-[520px] mb-4.5">First paragraph.</p>', false);
    }

    public function test_services_page_uses_admin_process_and_intro(): void
    {
        $this->setContent(['services' => [
            'intro' => 'Tell us what you need.',
            'process' => [['title' => 'Call', 'description' => 'We talk.'], ['title' => 'Shoot', 'description' => 'We shoot.']],
        ]]);

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('Tell us what you need.')
            ->assertSeeInOrder(['01', 'Call', '02', 'Shoot'])
            ->assertDontSee('Discover');
    }

    public function test_homepage_hero_and_sections_use_admin_content(): void
    {
        $this->setContent(['home' => [
            'hero_eyebrow' => 'Entebbe Studio',
            'hero_text' => 'Pictures of {words} and more.',
            'hero_words' => ['babies', 'pets'],
            'hero_tags' => ['50mm'],
            'cta_heading' => 'Say hello.',
        ]]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Entebbe Studio')
            ->assertSee('Pictures of <span class="tagline-rotator text-gold-bright"><span class="tagline-active">babies</span><span class="">pets</span></span> and more.', false)
            ->assertSee('>50mm</span>', false)
            ->assertDontSee('Golden Hour')
            ->assertSee('Say hello.');
    }

    public function test_hero_text_without_placeholder_has_no_rotator(): void
    {
        $this->setContent(['home' => ['hero_text' => 'Just honest photographs.']]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Just honest photographs.')
            ->assertDontSee('tagline-rotator', false);
    }

    public function test_homepage_about_teaser_mirrors_the_about_story(): void
    {
        SiteSetting::current()->update([
            'story_heading' => 'Our kind of photography.',
            'story_text' => "Opening line of the story.\n\nA later paragraph.",
            'story_image' => 'site-settings/story.jpg',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Our kind of photography.')
            ->assertSee('Opening line of the story.')
            ->assertDontSee('A later paragraph.')
            ->assertSee('https://cdn.r2.test/site-settings/story.jpg', false);
    }

    public function test_site_title_and_page_descriptions_are_editable(): void
    {
        $this->setContent([
            'site' => ['title' => 'Waka Studio'],
            'contact' => ['meta_description' => 'Write to us any time.', 'hero_eyebrow' => 'Say Hi'],
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('<title>Contact — Waka Studio</title>', false)
            ->assertSee('<meta name="description" content="Write to us any time.">', false)
            ->assertSee('Say Hi');
    }

    public function test_footer_tagline_and_response_time_are_editable(): void
    {
        $this->setContent(['site' => ['footer_tagline' => 'Weddings only', 'response_time' => 'Same day']], [
            'contact_email' => 'hello@example.com',
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Weddings only')
            ->assertSee('Same day')
            ->assertDontSee('Within 24–48 hours');
    }

    public function test_admin_edit_form_prefills_lists_and_saves_content(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $setting = SiteSetting::current();

        $component = Livewire::test(EditSiteSetting::class, ['record' => $setting->getRouteKey()])
            ->assertFormSet(['content.home.hero_words' => SiteSetting::contentDefault('home.hero_words')]);

        $component
            ->fillForm([
                'content.about.hero_heading' => 'About the Studio',
                'content.stats' => [['value' => '9', 'label' => 'Years']],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting->refresh();
        $this->assertSame('About the Studio', $setting->content('about.hero_heading'));
        $this->assertSame([['value' => '9', 'label' => 'Years']], $setting->content('stats'));
        $this->assertSame(SiteSetting::contentDefault('about.values'), $setting->content['about']['values']);
    }
}
