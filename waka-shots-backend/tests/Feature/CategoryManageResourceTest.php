<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Tests\TestCase;

class CategoryManageResourceTest extends TestCase
{
    public function test_categories_use_a_single_manage_records_route(): void
    {
        $pages = CategoryResource::getPages();

        $this->assertSame(['index'], array_keys($pages));
        $this->assertSame(ManageCategories::class, $pages['index']->getPage());
    }

    public function test_category_form_and_table_are_reused_for_modal_actions(): void
    {
        $form = CategoryForm::configure(Schema::make());
        $table = CategoriesTable::configure(Table::make($this->createMock(\Filament\Tables\Contracts\HasTable::class)));

        $this->assertCount(2, $form->getComponents());
        $this->assertSame(['name', 'slug'], array_map(
            static fn ($component): string => $component->getName(),
            $form->getComponents(),
        ));
        $this->assertSame(['name', 'slug', 'used_by', 'created_at'], array_keys($table->getColumns()));
        $this->assertNotNull($table->getAction('edit'));
        $this->assertNotNull($table->getAction('delete'));
    }
}
