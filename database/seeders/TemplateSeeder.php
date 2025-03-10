<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        $templates = [
            [
                'name' => 'Action Template',
                'description' => 'For your most adventurous and action videos',
                'audio' => 'audio/action.mp3',
                'icon' => 'icons/adventure.png',
            ],
            [
                'name' => 'Happy Template',
                'description' => 'Generate a video according to your happiest moments',
                'audio' => 'audio/happy.mp3',
                'icon' => 'icons/happy.png',
            ],
            [
                'name' => 'Rock Template',
                'description' => 'To the purest rock and roll',
                'audio' => 'audio/rock.mp3',
                'icon' => 'icons/rock.png',
            ],
            [
                'name' => 'Soul Template',
                'description' => 'Soul moments',
                'audio' => 'audio/soul.mp3',
                'icon' => 'icons/soul.png',
            ]
        ];

        foreach ($templates as $templateData) {
            $template = Template::create([
                'name' => $templateData['name'],
                'description' => $templateData['description'],
            ]);

            // Asociar el archivo de audio
            $audioPath = storage_path('app/public/' . $templateData['audio']);
            if (file_exists($audioPath)) {
                $template->addMedia($audioPath)
                         ->preservingOriginal() // Asegura que el archivo original no sea eliminado
                         ->toMediaCollection('audio');
            } else {
                echo "Archivo de audio no encontrado: " . $audioPath . "\n";
            }

            // Asociar el archivo de icono
            $iconPath = storage_path('app/public/' . $templateData['icon']);
            if (file_exists($iconPath)) {
                $template->addMedia($iconPath)
                         ->preservingOriginal() // Asegura que el archivo original no sea eliminado
                         ->toMediaCollection('icon');
            } else {
                echo "Archivo de icono no encontrado: " . $iconPath . "\n";
            }
        }
    }
}
