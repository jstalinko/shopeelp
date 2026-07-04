<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (\App\Models\User::where('email', 'admin@admin.com')->count() === 0) {
            User::factory()->create([
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password')
            ]);
        }

        if (\App\Models\Template::count() === 0) {
            \App\Models\Template::create([
                'name' => 'Shopee Promo Redirect',
                'description' => 'Redirects clicks to target Shopee URLs with tracking.',
                'image' => 'shopee_logo.png',
                'template_path' => 'templates/shopee_redirect',
                'config' => json_encode(['delay' => 2, 'auto_redirect' => true]),
                'active' => true,
            ]);

            \App\Models\Template::create([
                'name' => 'Shopee Glassmorphic Landing Page',
                'description' => 'Beautiful premium glassmorphic layout displaying custom products.',
                'image' => 'landing_glass.png',
                'template_path' => 'templates/shopee_landing',
                'config' => json_encode(['theme' => 'dark', 'show_comments' => true]),
                'active' => true,
            ]);
        }
    }
}
