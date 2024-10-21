<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Notes;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userKira = User::where('email', 'kira@gmail.com')->first();
        $userJotaro = User::where('email', 'jotaro@gmail.com')->first();
        $userDio = User::where('email', 'dio@gmail.com')->first();

        // Dapatkan Category IDs
        $categoryGeneral = Category::where('name', 'General')->first();
        $categoryWork = Category::where('name', 'Work')->first();
        $categoryPersonal = Category::where('name', 'Personal')->first();

        // Buat Notes
        $notes = [
            [
                'title' => 'Kira\'s General Note',
                'content' => 'This is a general note by Kira.',
                'status' => 'regular',
                'users_id' => $userKira->id,
                'category_ids' => [$categoryGeneral->id],
            ],
            [
                'title' => 'Jotaro\'s Work Note',
                'content' => 'This is a work-related note by Jotaro.',
                'status' => 'regular',
                'users_id' => $userJotaro->id,
                'category_ids' => [$categoryWork->id],
            ],
            [
                'title' => 'Dio\'s Personal Note',
                'content' => 'This is a personal note by Dio.',
                'status' => 'favorite',
                'users_id' => $userDio->id,
                'category_ids' => [$categoryPersonal->id],
            ],
            [
                'title' => 'Dio\'s Over Heavens Note',
                'content' => 'This is a personal note by Dio.',
                'status' => 'favorite',
                'users_id' => $userDio->id,
                'category_ids' => null,
            ],
        ];

        foreach ($notes as $noteData) {
            // Buat note
            $note = Notes::create([
                'title' => $noteData['title'],
                'content' => $noteData['content'],
                'status' => $noteData['status'],
                'users_id' => $noteData['users_id'],
            ]);

            // Sync dengan kategori
            $note->categories()->sync($noteData['category_ids']);
        }
    }
}
