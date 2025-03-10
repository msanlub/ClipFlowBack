<?php

namespace App\Services\Templates;

class SoulTemplate implements VideoTemplateInterface
{
    public function getCommand(array $params, string $outputPath): string
    {
        $img1 = $params['img1'];
        $img2 = $params['img2'];
        $img3 = $params['img3'];
        $img4 = $params['img4'];
        $text1 = $params['text1'];
        $text2 = $params['text2'];

        // Ruta del audio específico para este template
        $audioFile = storage_path('app/public/audio/soul.mp3');

        if (!file_exists($audioFile)) {
            throw new \Exception('Archivo de audio no encontrado.');
        }

        return "ffmpeg -y " .
            // Cargar las imágenes
            " -loop 1 -t 5 -i {$img1}" .
            " -loop 1 -t 5 -i {$img2}" .
            " -loop 1 -t 5 -i {$img3}" .
            " -loop 1 -t 5 -i {$img4}" .
            " -i {$audioFile}" .
            // Aplicar filtros para escalar y colocar los textos
            " -filter_complex \"" .
                // Imagen 1 con texto 1 alineado al lado izquierdo
                "[0:v]scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2, " .
                "drawtext=text='{$text1}':fontcolor=white:fontsize=56:x=20:y=(h-text_h)/2, " .
                "setpts=PTS-STARTPTS[v0];" .
                // Imagen 2 sin texto
                "[1:v]scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2, " .
                "setpts=PTS-STARTPTS[v1];" .
                // Imagen 3 con texto 2 alineado al lado derecho
                "[2:v]scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2, " .
                "drawtext=text='{$text2}':fontcolor=white:fontsize=56:x=(w-text_w)-20:y=(h-text_h)/2, " .
                "setpts=PTS-STARTPTS[v2];" .
                // Imagen 4 sin texto
                "[3:v]scale=1080:1920:force_original_aspect_ratio=decrease,pad=1080:1920:(ow-iw)/2:(oh-ih)/2, " .
                "setpts=PTS-STARTPTS[v3];" .
                // Concatenar las imágenes y crear el video
                "[v0][v1][v2][v3]concat=n=4:v=1:a=0,format=yuv420p[v]\" " .
            // Mapeo de video y audio
            " -map \"[v]\" -map 4:a -shortest {$outputPath}";
    }
}
