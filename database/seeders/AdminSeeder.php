<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            [
                'name' => 'Admin One',
                'email' => 'admin1@example.com',
                'password' => Hash::make('password'),
                'birthday' => '1990-01-01',
                'authority' => 'administrator',
            ],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@example.com',
                'password' => Hash::make('password'),
                'birthday' => '1985-02-15',
                'authority' => 'administrator',
            ],
            [
                'name' => 'Admin Three',
                'email' => 'admin3@example.com',
                'password' => Hash::make('password'),
                'birthday' => '1992-03-30',
                'authority' => 'editer',
            ],
            [
                'name' => 'Admin Four',
                'email' => 'admin4@example.com',
                'password' => Hash::make('password'),
                'birthday' => '1988-07-22',
                'authority' => 'viewer',
            ],
            [
                'name' => 'Admin Five',
                'email' => 'admin5@example.com',
                'password' => Hash::make('password'),
                'birthday' => '1995-11-05',
                'authority' => 'viewer',
            ]
        ];

        foreach ($admins as $admin) {
            Admin::create($admin);
        }
    }
}
