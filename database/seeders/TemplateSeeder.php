<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('templates')->insert([
            [
                'name' => 'Action Template',
                'description' => 'Para tus videos más aventureros y de acción',
                'file_path' => 'app\Templates\ActionTemplate.php',
                'audio_path' => 'app\Templates\audio\action.mp3',
                'icon_path' => 'app\Templates\iconoTemplates\adventure.png',
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
            [
                'name' => 'Happy Template',
                'description' => 'Genera un video acorde a tus momentos más felices',
                'file_path' => 'app\Templates\HappyTemplate.php',
                'audio_path' => 'app\Templates\audio\happy.mp3',
                'icon_path' => 'app\Templates\iconoTemplates\happy.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rock Template',
                'description' => 'Al más puro rock and roll',
                'file_path' => 'app\Templates\RockTemplate.php',
                'audio_path' => 'app\Templates\audio\rock.mp3',
                'icon_path' => 'app\Templates\iconoTemplates\rock.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Soul Template',
                'description' => 'Momentos soul',
                'file_path' => 'app\Templates\SoulTemplate.php',
                'audio_path' => '.app\Templates\audio\soul.mp3',
                'icon_path' => 'app\Templates\iconoTemplates\soul.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
        
    }
}
