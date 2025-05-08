<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


class UserRoleSeeder extends Seeder
{
    public function run(): void
    {

        // Create roles
        Role::firstOrCreate(['name' => 'agent']);
        Role::firstOrCreate(['name' => 'supervisor']);
        Role::firstOrCreate(['name' => 'admin']);

        // Create uesrs for those roles

        $supervisor = User::create([
            'name' => 'Supervisor User',
            'email' => 'supervisor@example.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
        ]);
        $supervisor->assignRole('supervisor');

        $agent1 = User::create([
            'name' => 'Agent One',
            'email' => 'agent1@example.com',
            'password' => Hash::make('password'),
        ]);
        $agent1->assignRole('agent');

        $agent2 = User::create([
            'name' => 'Agent Two',
            'email' => 'agent2@example.com',
            'password' => Hash::make('password'),
        ]);
        $agent2->assignRole('agent');

        $this->command->info('Users with roles created successfully.');
    }
}
