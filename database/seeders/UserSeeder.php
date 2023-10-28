<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// # CI IMPORTIAMO L'Hash PER NASCONDERE LA PASSWORD NEL DB
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Admin";
        $user->email = "admin@admin.it";
        // # QUI USIAMO UN METODO PER HASHARE LA PASSWORD
        $user->password = Hash::make("password");
        $user->save();
    }
}
