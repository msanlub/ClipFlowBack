<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'file_path',  // Ruta del archivo de la plantilla
        'audio_path', // ruta del archivo de audio
        'icon_path', // ruta del archivo de icono
    ];

    /**
     * Relación: Una plantilla puede ser favorita de varios usuarios.
     */
    public function usersWhoFavorited(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
