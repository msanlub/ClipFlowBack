<?php

namespace App\Services\Templates;

interface VideoTemplateInterface
{
    /**
     * Recibe los parámetros necesarios y retorna el comando ffmpeg a ejecutar.
     *
     * @param array $params
     * @param string $outputPath
     * @param string $audioFile
     * @return string
     */
    public function getCommand(array $params, string $outputPath, string $audioFile): string;
}
