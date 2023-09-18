<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        for($i = 1; $i <= 10; $i++){
            User::create([
                'name' => "Test User Name $i",
                'email' => "test_$i@example.com",
                'password' => Hash::make('password123')
            ]);
            for($j = 1; $j <= 10; $j++){
                Post::create([
                    "title" => "Test Post Title i=>$i, j => $j",
                    'body' => "Test Post Body i=>$i, j => $j",
                    'creator' => $i,
                    'slug' => "test-slug j=>$j",
                    'status' => 1,
                ]);
                for($k=1; $k <= 10; $k++){
                    Comment::create([
                        'post_id' => $j,
                        'body' => "Test Comment Body i=>$i, j => $j, k => $k",
                        'creator' => $i,
                        'slug' => "test-slug k=>$k",
                        'status' => 1
                    ]);
                }
            }
            
        }
    }
}
