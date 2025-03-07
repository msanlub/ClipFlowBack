<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();


        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);
        Role::create(['name' => 'user']);
        Permission::create(['name' => 'create post']);
        Permission::create(['name' => 'edit post']);
        Permission::create(['name' => 'delete post']);
        $admin = Role::findByName('admin');
        $admin->givePermissionTo(['create post', 'edit post', 'delete post']);
        $editor = Role::findByName('editor');
        $editor->givePermissionTo(['create post', 'edit post']);
        $user = Role::findByName('user');
        $user->givePermissionTo('create post','delete post');

        $this->call([
            TemplateSeeder::class, // Llamar al seeder de plantilals
        ]);

    }
}
