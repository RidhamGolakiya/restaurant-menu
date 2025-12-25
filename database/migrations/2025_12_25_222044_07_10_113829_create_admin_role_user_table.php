<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = Role::where('name', 'admin')->first();
        if(!$role) {
            $role = Role::create(['name' => 'admin']);
        }
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'status' => \App\Models\User::ACTIVE,
            'password' => bcrypt('123456'),
        ]);

        $user->assignRole($role);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
