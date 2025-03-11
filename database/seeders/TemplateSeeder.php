<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        // Primero, eliminamos todas las plantillas existentes para evitar duplicados
        // Nota: Esto también eliminará todos los medios asociados debido a las relaciones
        Template::truncate();
        
        $templates = [
            [
                'name' => 'Action Template',
                'description' => 'For your most adventurous and action videos',
                'audio' => 'action.mp3',
                'icon' => 'adventure.png',
            ],
            [
                'name' => 'Happy Template',
                'description' => 'Generate a video according to your happiest moments',
                'audio' => 'happy.mp3',
                'icon' => 'happy.png',
            ],
            [
                'name' => 'Rock Template',
                'description' => 'To the purest rock and roll',
                'audio' => 'rock.mp3',
                'icon' => 'rock.png',
            ],
            [
                'name' => 'Soul Template',
                'description' => 'Soul moments',
                'audio' => 'soul.mp3',
                'icon' => 'soul.png',
            ]
        ];

        // Asegurarse de que los directorios existan en storage/app/public
        $audioDir = storage_path('app/public/audio');
        $iconDir = storage_path('app/public/icons');
        
        if (!File::exists($audioDir)) {
            File::makeDirectory($audioDir, 0755, true);
        }
        
        if (!File::exists($iconDir)) {
            File::makeDirectory($iconDir, 0755, true);
        }
        
        // Publicar archivos de storage si aún no se ha hecho
        $this->command->info('Asegurando que los archivos de storage estén disponibles públicamente...');
        $this->command->call('storage:link');

        foreach ($templates as $templateData) {
            // Crear la plantilla
            $template = Template::create([
                'name' => $templateData['name'],
                'description' => $templateData['description'],
            ]);

            // Rutas completas a los archivos
            $audioPath = storage_path('app/public/audio/' . $templateData['audio']);
            $iconPath = storage_path('app/public/icons/' . $templateData['icon']);
            
            // Verificar y asociar el archivo de audio
            if (file_exists($audioPath)) {
                $this->command->info("Añadiendo audio: $audioPath");
                $template->addMedia($audioPath)
                         ->preservingOriginal() // Conserva el archivo original
                         ->usingName($templateData['name'] . ' Audio')
                         ->toMediaCollection('audio');
            } else {
                $this->command->error("Archivo de audio no encontrado: $audioPath");
                
                // Intenta buscar en otras posibles rutas en entorno Docker
                $altAudioPath = base_path('storage/app/public/audio/' . $templateData['audio']);
                if (file_exists($altAudioPath)) {
                    $this->command->info("Audio encontrado en ruta alternativa: $altAudioPath");
                    $template->addMedia($altAudioPath)
                             ->preservingOriginal() // Conserva el archivo original
                             ->usingName($templateData['name'] . ' Audio')
                             ->toMediaCollection('audio');
                }
            }

            // Verificar y asociar el archivo de icono
            if (file_exists($iconPath)) {
                $this->command->info("Añadiendo icono: $iconPath");
                $template->addMedia($iconPath)
                         ->preservingOriginal() // Conserva el archivo original
                         ->usingName($templateData['name'] . ' Icon')
                         ->toMediaCollection('icon');
            } else {
                $this->command->error("Archivo de icono no encontrado: $iconPath");
                
                // Intenta buscar en otras posibles rutas en entorno Docker
                $altIconPath = base_path('storage/app/public/icons/' . $templateData['icon']);
                if (file_exists($altIconPath)) {
                    $this->command->info("Icono encontrado en ruta alternativa: $altIconPath");
                    $template->addMedia($altIconPath)
                             ->preservingOriginal() // Conserva el archivo original
                             ->usingName($templateData['name'] . ' Icon')
                             ->toMediaCollection('icon');
                }
            }
        }
        
        $this->command->info('¡Seeder de plantillas completado!');
    }
}