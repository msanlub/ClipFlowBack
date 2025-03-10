<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Template extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relación: Una plantilla puede ser favorita de varios usuarios.
     */
    public function usersWhoFavorited(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('audio')
            ->singleFile()
            ->acceptsMimeTypes(['audio/mpeg', 'audio/wav', 'audio/ogg','audio/mp3']); 

        $this
            ->addMediaCollection('icon')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml']);

        $this->addMediaCollection('generated_videos');
    }

    /**
     * Obtener la URL del audio asociado a la plantilla.
     *
     * @return string|null
     */
    public function getAudioUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('audio');

        return $media ? $media->getFullUrl() : null;
    }

    /**
     * Obtener la URL del icono asociado a la plantilla.
     *
     * @return string|null
     */
    public function getIconUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('icon');

        return $media ? $media->getFullUrl() : null;
    }
}
