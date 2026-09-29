<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->persistTabInQueryString()
                    ->columnSpanFull()
                    ->tabs([
                        self::generalTab(),
                        self::homeTab(),
                        self::aboutTab(),
                        self::servicesTab(),
                        self::portfolioTab(),
                        self::filmsTab(),
                        self::journalTab(),
                        self::contactTab(),
                    ]),
            ]);
    }

    private static function generalTab(): Tab
    {
        return Tab::make('General')
            ->schema([
                Section::make('Studio Profile')
                    ->schema([
                        TextInput::make('studio_name')
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_email')
                            ->email()
                            ->label('Contact Email'),
                        TextInput::make('contact_phone')
                            ->label('Contact Phone'),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number')
                            ->helperText('Include the country code, e.g. +256 700 000000 (spaces and "+" are fine). Used for the site\'s WhatsApp links.'),
                        Textarea::make('address')
                            ->columnSpanFull(),
                        self::text('site.response_time', 'Response Time')
                            ->helperText('Shown in the Studio Details box on the Contact page.'),
                    ])
                    ->columns(3),
                Section::make('Social Media Links')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->url()
                            ->label('Instagram URL'),
                        TextInput::make('youtube_url')
                            ->url()
                            ->label('YouTube URL'),
                        TextInput::make('facebook_url')
                            ->url()
                            ->label('Facebook URL'),
                        TextInput::make('tiktok_url')
                            ->url()
                            ->label('TikTok URL'),
                        TextInput::make('x_url')
                            ->url()
                            ->label('X (Twitter) URL'),
                        TextInput::make('google_review_url')
                            ->url()
                            ->label('Google Review Link')
                            ->helperText('Direct link to leave a Google review. Shown to gallery clients right after they submit a testimonial.'),
                    ])
                    ->columns(3),
                Section::make('Search & Sharing')
                    ->description('How the site appears in Google results and link previews.')
                    ->schema([
                        self::text('site.title', 'Site Title')
                            ->helperText('Used as the browser tab title and after every page name, e.g. "About — Site Title".'),
                        self::area('site.description', 'Default Description', 3)
                            ->helperText('Used on the homepage and any page without its own description. Aim for under 160 characters.'),
                    ]),
                Section::make('Studio Stats')
                    ->description('The numbers band on the homepage and About page.')
                    ->schema([
                        Repeater::make('content.stats')
                            ->hiddenLabel()
                            ->schema([
                                TextInput::make('value')
                                    ->required()
                                    ->maxLength(20)
                                    ->placeholder('120+'),
                                TextInput::make('label')
                                    ->required()
                                    ->maxLength(60)
                                    ->placeholder('Stories Told'),
                            ])
                            ->columns(2)
                            ->maxItems(4)
                            ->reorderable()
                            ->addActionLabel('Add stat'),
                    ]),
                Section::make('WhatsApp Messages')
                    ->description('The message pre-filled when a visitor taps the WhatsApp button.')
                    ->schema([
                        self::area('site.whatsapp_message', 'Public Site', 2),
                        self::area('site.gallery_whatsapp_message', 'Client Galleries', 2),
                    ])
                    ->columns(2),
                Section::make('Footer')
                    ->schema([
                        Textarea::make('footer_about_text')
                            ->label('About Blurb')
                            ->placeholder('Capturing moments. Creating stories. A photography studio based in Kampala, working across Uganda and East Africa.')
                            ->helperText('Short description under the studio name in the footer. Leave blank to keep the default.')
                            ->columnSpanFull(),
                        self::text('site.footer_tagline', 'Bottom Tagline')
                            ->helperText('The line beside the copyright notice.'),
                    ]),
            ]);
    }

    private static function homeTab(): Tab
    {
        return Tab::make('Home')
            ->schema([
                Section::make('Hero')
                    ->schema([
                        self::image('home_hero_image', 'Background Image')
                            ->helperText('Full-bleed image behind the homepage headline. Also used as the default link-preview image. Leave blank to keep the default.'),
                        self::text('home.hero_eyebrow', 'Eyebrow Label'),
                        Textarea::make('hero_tagline')
                            ->label('Headline')
                            ->placeholder('Stories, beautifully captured.')
                            ->helperText('Leave blank to keep the default.'),
                        self::area('home.hero_text', 'Intro Text', 2)
                            ->helperText('Put {words} where the rotating words should appear.'),
                        TagsInput::make('content.home.hero_words')
                            ->label('Rotating Words')
                            ->helperText('Press Enter after each word. They cycle in the intro text.'),
                        TagsInput::make('content.home.hero_tags')
                            ->label('Floating Tags')
                            ->helperText('Small labels drifting over the hero on desktop. Up to 5 are shown.'),
                    ])
                    ->columns(1),
                self::headingSection('Selected Work', 'home.work'),
                Section::make('About Teaser')
                    ->description('The image, heading and first paragraph come from the "Our Story" section on the About tab.')
                    ->schema([
                        self::text('home.about_eyebrow', 'Eyebrow Label'),
                    ]),
                self::headingSection('Services Teaser', 'home.services'),
                Section::make('Partners Band')
                    ->schema([
                        self::image('home_partners_image', 'Background Image')
                            ->columnSpanFull(),
                        self::text('home.partners_eyebrow', 'Eyebrow Label'),
                        self::text('home.partners_heading', 'Heading'),
                    ])
                    ->columns(2),
                self::headingSection('Testimonials', 'home.testimonials'),
                self::headingSection('Journal Teaser', 'home.journal'),
                self::headingSection('Closing Call to Action', 'home.cta'),
            ]);
    }

    private static function aboutTab(): Tab
    {
        return Tab::make('About')
            ->schema([
                self::heroSection('about'),
                Section::make('Our Story')
                    ->description('The "Who We Are" section. The homepage About teaser uses the same image, heading and first paragraph.')
                    ->schema([
                        self::image('story_image', 'Story Image')
                            ->columnSpanFull(),
                        self::text('about.story_eyebrow', 'Eyebrow Label'),
                        TextInput::make('story_heading')
                            ->label('Heading')
                            ->maxLength(150)
                            ->placeholder('More than photographs. Moments with meaning.'),
                        Textarea::make('story_text')
                            ->label('Story')
                            ->rows(6)
                            ->helperText('Separate paragraphs with a blank line. Leave blank to keep the default.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Meet the Photographer')
                    ->schema([
                        self::image('photographer_image', 'Photographer Image')
                            ->columnSpanFull(),
                        TextInput::make('photographer_heading')
                            ->label('Heading')
                            ->maxLength(150)
                            ->placeholder('Behind every frame.')
                            ->columnSpanFull(),
                        Textarea::make('photographer_bio')
                            ->label('Bio')
                            ->rows(5)
                            ->helperText('Separate paragraphs with a blank line. Leave blank to keep the default.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Values')
                    ->schema([
                        self::text('about.values_eyebrow', 'Eyebrow Label'),
                        self::text('about.values_heading', 'Heading'),
                        self::titledList('content.about.values', 'Add value')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                self::headingSection('Call to Action', 'about.cta'),
            ]);
    }

    private static function servicesTab(): Tab
    {
        return Tab::make('Services')
            ->schema([
                self::heroSection('services'),
                Section::make('Intro')
                    ->schema([
                        self::area('services.intro', 'Intro Paragraph', 3)->hiddenLabel(),
                    ]),
                Section::make('Process')
                    ->description('The "How We Work" steps. They are numbered automatically.')
                    ->schema([
                        self::text('services.process_eyebrow', 'Eyebrow Label'),
                        self::text('services.process_heading', 'Heading'),
                        self::titledList('content.services.process', 'Add step')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                self::headingSection('Call to Action', 'services.cta'),
            ]);
    }

    private static function portfolioTab(): Tab
    {
        return Tab::make('Portfolio')
            ->schema([
                Section::make('Page Header')
                    ->schema([
                        self::image('portfolio_hero_image', 'Hero Background Image')
                            ->columnSpanFull(),
                        TextInput::make('portfolio_hero_eyebrow')
                            ->label('Eyebrow Label')
                            ->maxLength(100)
                            ->placeholder('Our Work'),
                        TextInput::make('portfolio_hero_heading')
                            ->label('Heading')
                            ->maxLength(100)
                            ->placeholder('Portfolio'),
                        self::metaDescription('portfolio'),
                    ])
                    ->columns(2),
                self::headingSection('Call to Action', 'portfolio.cta'),
            ]);
    }

    private static function filmsTab(): Tab
    {
        return Tab::make('Films')
            ->schema([
                self::heroSection('films'),
                Section::make('Intro')
                    ->schema([
                        self::area('films.intro', 'Intro Paragraph', 3)->hiddenLabel(),
                    ]),
                self::headingSection('Call to Action', 'films.cta'),
            ]);
    }

    private static function journalTab(): Tab
    {
        return Tab::make('Journal')
            ->schema([
                self::heroSection('journal'),
                self::headingSection('Call to Action', 'journal.cta')
                    ->description('The heading is also used at the end of each journal post.'),
            ]);
    }

    private static function contactTab(): Tab
    {
        return Tab::make('Contact')
            ->schema([
                Section::make('Page Header')
                    ->schema([
                        self::image('content.contact.hero_image', 'Hero Background Image')
                            ->columnSpanFull(),
                        self::text('contact.hero_eyebrow', 'Eyebrow Label'),
                        TextInput::make('contact_tagline')
                            ->label('Heading')
                            ->maxLength(150)
                            ->placeholder("Let's create something unforgettable."),
                        self::metaDescription('contact'),
                    ])
                    ->columns(2),
                Section::make('Beside the Form')
                    ->schema([
                        self::image('contact_image', 'Studio Image')
                            ->helperText('Shown beside the contact form. Leave blank to keep the default.'),
                    ]),
            ]);
    }

    /** Hero image, eyebrow, heading and search description for a page. */
    private static function heroSection(string $page): Section
    {
        return Section::make('Page Header')
            ->schema([
                self::image("content.{$page}.hero_image", 'Hero Background Image')
                    ->columnSpanFull(),
                self::text("{$page}.hero_eyebrow", 'Eyebrow Label'),
                self::text("{$page}.hero_heading", 'Heading'),
                self::metaDescription($page),
            ])
            ->columns(2);
    }

    /** An eyebrow label and heading pair, keyed "{prefix}_eyebrow" and "{prefix}_heading". */
    private static function headingSection(string $title, string $prefix): Section
    {
        return Section::make($title)
            ->schema([
                self::text("{$prefix}_eyebrow", 'Eyebrow Label'),
                self::text("{$prefix}_heading", 'Heading'),
            ])
            ->columns(2);
    }

    private static function metaDescription(string $page): Textarea
    {
        return self::area("{$page}.meta_description", 'Search Description', 2)
            ->helperText('Shown in Google results and link previews. Aim for under 160 characters.')
            ->columnSpanFull();
    }

    /** Numbered title + description rows, used for values and process steps. */
    private static function titledList(string $name, string $addLabel): Repeater
    {
        return Repeater::make($name)
            ->hiddenLabel()
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(100),
                Textarea::make('description')
                    ->required()
                    ->rows(2),
            ])
            ->reorderable()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ->addActionLabel($addLabel);
    }

    /** Text input for a `content` key, with its default as the placeholder. */
    private static function text(string $key, string $label): TextInput
    {
        return TextInput::make("content.{$key}")
            ->label($label)
            ->maxLength(255)
            ->placeholder(SiteSetting::contentDefault($key));
    }

    private static function area(string $key, string $label, int $rows): Textarea
    {
        return Textarea::make("content.{$key}")
            ->label($label)
            ->rows($rows)
            ->placeholder(SiteSetting::contentDefault($key));
    }

    private static function image(string $name, string $label): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->disk('r2')
            ->directory('site-settings')
            ->acceptedFileTypes(['image/*'])
            ->image()
            ->imageEditor()
            ->helperText('Leave blank to keep the default.');
    }
}
