<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            LegacyContentSeeder::class,
            ContentSeeder::class,
        ]);

        // Yalnızca geliştirme ortamında deneme yöneticisi. Canlıda: php artisan make:filament-user
        if (app()->environment('local') && ! User::exists()) {
            User::create([
                'name' => 'Deneme Yönetici',
                'email' => 'admin@example.com',
                'password' => 'password',
                'is_active' => true,
            ]);
            $this->command?->warn('Deneme yöneticisi: admin@example.com / password (yalnızca local ortam)');
        }
    }
}
