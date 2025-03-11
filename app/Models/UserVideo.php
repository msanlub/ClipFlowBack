<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @OA\Schema(
 *    title="UserVideo",
 *    description="UserVideo model",
 *    required={"user_id", "template_id", "media_id"},
 *    @OA\Property(
 *        property="id",
 *        type="integer",
 *        description="ID",
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="user_id",
 *        type="integer",
 *        description="User ID",
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="template_id",
 *        type="integer",
 *        description="Template ID",
 *        example="2"
 *    ),
 *    @OA\Property(
 *        property="media_id",
 *        type="integer",
 *        description="Media ID",
 *        example="3"
 *    ),
 *    @OA\Property(
 *        property="created_at",
 *        type="string",
 *        format="date-time",
 *        description="Creation Timestamp",
 *        example="2025-02-26T12:00:00.000000Z"
 *    ),
 *    @OA\Property(
 *        property="updated_at",
 *        type="string",
 *        format="date-time",
 *        description="Update Timestamp",
 *        example="2025-02-26T12:30:00.000000Z"
 *    )
 * )
 */
class UserVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'media_id',  // ID del video en la tabla media
    ];

    /**
     * Relación con Usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Plantilla usada para crear el video.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Relación con el archivo multimedia (video) en la tabla 'media'.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
