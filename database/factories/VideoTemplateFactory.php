<?php

namespace Database\Factories;

class VideoTemplateFactory
{
    /**
     * Retorna una instancia de VideoTemplateInterface según el ID.
     *
     * @param int $templateId
     * @return VideoTemplateInterface|null
     */
    public static function make(int $templateId): ?VideoTemplateInterface
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
                return null;
        }
    }
}
