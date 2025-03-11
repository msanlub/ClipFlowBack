<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *    title="Favorite",
 *    description="Favorite model",
 *    required={"user_id", "template_id"},
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
 *        example="1"
 *    )
 * )
 */
class Favorite extends Model
{
    use HasFactory;

    protected $table = 'favorites'; 

    protected $fillable = [
        'user_id',
        'template_id',
    ];

    public $timestamps = false;

    /**
     * Relación con Usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con Plantilla.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
