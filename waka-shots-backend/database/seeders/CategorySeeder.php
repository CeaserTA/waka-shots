<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $categories = [
                ['name' => 'Weddings', 'slug' => 'weddings'],
                ['name' => 'Portraits', 'slug' => 'portraits'],
                ['name' => 'Commercial', 'slug' => 'commercial'],
                ['name' => 'Events', 'slug' => 'events'],
            ];

            foreach ($categories as $category) {
                Category::create($category);
            }
    }
}
