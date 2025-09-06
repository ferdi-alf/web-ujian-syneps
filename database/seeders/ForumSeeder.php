<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\User;

class ForumSeeder extends Seeder
{
    public function run()
    {
        // Get admin user
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            // Create a test admin if none exists
            $admin = User::create([
                'name' => 'Admin Test',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'avatar' => 'default.jpg'
            ]);
        }

        // Create test posts
        $post1 = ForumPost::create([
            'user_id' => $admin->id,
            'content' => 'Selamat datang di Forum Alumni! Mari berbagi pengalaman dan informasi.',
            'likes_count' => 0,
            'comments_count' => 0
        ]);

        $post2 = ForumPost::create([
            'user_id' => $admin->id,
            'content' => 'Informasi lowongan kerja terbaru akan dibagikan di sini secara berkala.',
            'likes_count' => 0,
            'comments_count' => 0
        ]);

        // Create test comments
        $comment1 = ForumComment::create([
            'user_id' => $admin->id,
            'post_id' => $post1->id,
            'content' => 'Terima kasih atas informasinya!',
            'parent_id' => null
        ]);

        $comment2 = ForumComment::create([
            'user_id' => $admin->id,
            'post_id' => $post1->id,
            'content' => 'Sangat membantu sekali.',
            'parent_id' => null
        ]);

        // Create a reply
        $reply1 = ForumComment::create([
            'user_id' => $admin->id,
            'post_id' => $post1->id,
            'content' => 'Sama-sama! Semoga bermanfaat.',
            'parent_id' => $comment1->id
        ]);

        // Update comment counts
        $post1->update(['comments_count' => 3]);
        $post2->update(['comments_count' => 0]);

        $this->command->info('Forum data seeded successfully!');
    }
}
