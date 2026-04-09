<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@upgris.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'npm' => null,
        ]);

        // Create Sample Events
        Event::create([
            'nama_event' => 'Kuliah Pagi - Pemrograman Web',
            'latitude' => -6.9883196665620675,
            'longitude' => 110.43569087874343,
            'radius' => 50,
            'start_time' => now()->startOfDay()->addHours(7),
            'end_time' => now()->startOfDay()->addHours(23),
            'is_active' => true,
            'google_form_url' => 'https://docs.google.com/forms/d/e/1FAIpQLScWHAvYLOjm1a0QHKNSzR1qkNcWY1NkrTeilbXk_s5p_J_7AQ/viewform',
            'created_by' => $admin->id,
        ]);

        Event::create([
            'nama_event' => 'Praktikum Basis Data',
            'latitude' => -6.9883196665620675,
            'longitude' => 110.43569087874343,
            'radius' => 50,
            'start_time' => now()->startOfDay()->addHours(13),
            'end_time' => now()->startOfDay()->addHours(23),
            'is_active' => true,
            'google_form_url' => null,
            'created_by' => $admin->id,
        ]);

        Event::create([
            'nama_event' => 'Seminar Nasional IT 2026',
            'latitude' => -6.9883196665620675,
            'longitude' => 110.43569087874343,
            'radius' => 100,
            'start_time' => now()->startOfDay()->addHours(8),
            'end_time' => now()->addDays(1)->endOfDay(),
            'is_active' => true,
            'google_form_url' => null,
            'created_by' => $admin->id,
        ]);
    }
}
