<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Todo;
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

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create sample todos for the test user
        Todo::factory(15)->create([
            'user_id' => $user->id,
        ]);

        // Create some specific status todos
        Todo::factory(5)->pending()->create(['user_id' => $user->id]);
        Todo::factory(3)->inProgress()->create(['user_id' => $user->id]);
        Todo::factory(7)->completed()->create(['user_id' => $user->id]);
        Todo::factory(2)->highPriority()->create(['user_id' => $user->id]);
        Todo::factory(3)->overdue()->create(['user_id' => $user->id]);
    }
}
