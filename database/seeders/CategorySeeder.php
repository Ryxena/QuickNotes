<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'General', 'users_id' => null, 'is_public' => true],
            ['name' => 'Work', 'users_id' => null, 'is_public' => true],
            ['name' => 'Personal', 'users_id' => null, 'is_public' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
