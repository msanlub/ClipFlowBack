<?php

namespace Database\Factories;

use App\Services\Templates\VideoTemplateInterface;
use App\Services\Templates\ActionTemplate;
use App\Services\Templates\HappyTemplate;
use App\Services\Templates\SoulTemplate;
use App\Services\Templates\RockTemplate;

class VideoTemplateFactory
{
    /**
     * Retorna una instancia de VideoTemplateInterface según el ID.
     *
     * @param int $templateId El ID de la plantilla a crear.
     * @return VideoTemplateInterface
     * @throws \InvalidArgumentException si se proporciona un ID de plantilla no válido.
     */
    public static function make(int $templateId): VideoTemplateInterface
    {
        switch ($templateId) {
            case 1:
                return new ActionTemplate();
            case 2:
                return new HappyTemplate();
            case 3:
                return new SoulTemplate();
            case 4:
                return new RockTemplate();
            default:
                throw new \InvalidArgumentException("Template ID {$templateId} not found.");
        }
    }
}
