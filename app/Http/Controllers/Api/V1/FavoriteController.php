<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use App\Models\Template; 

class FavoriteController extends Controller
{
    /**
     * Constructor para aplicar el middleware de autenticación.
     */
    public function __construct()
    {
        $this->middleware('auth:api'); // Aplica el middleware auth:api a todas las acciones
    }

    /**
     * Lista los templates favoritos del usuario autenticado.
     */
    public function index()
    {
        // Obtener los templates favoritos del usuario autenticado
        $favorites = Favorite::with(['template' => function($query) {
            $query->with('media'); // Cargar explícitamente la relación media
        }])
            ->where('user_id', Auth::id())
            ->get();

        // Formatear la respuesta para incluir las URLs de audio e icono
        $formattedFavorites = $favorites->map(function ($favorite) {
            return [
                'id' => $favorite->id,
                'template_id' => $favorite->template_id,
                'template' => [
                    'id' => $favorite->template->id,
                    'name' => $favorite->template->name,
                    'description' => $favorite->template->description,
                    'audio_url' => $favorite->template->audio_url, 
                    'icon_url' => $favorite->template->icon_url,  
                ],
            ];
        });

        return response()->json($formattedFavorites);
    }

    /**
     * Añade un template a la lista de favoritos del usuario autenticado.
     */
    public function store(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'template_id' => 'required|exists:templates,id',
        ]);

        // Verificar si el template ya está en favoritos
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('template_id', $request->template_id)
            ->first();

        if ($existingFavorite) {
            return response()->json(['message' => 'Template already in favorites.'], 409); // Conflicto ya existe en favoritos
        }

        // Crear un nuevo favorito
        $favorite = Favorite::create([
            'user_id' => Auth::id(), // Obtener el ID del usuario autenticado
            'template_id' => $request->template_id,
        ]);

        // Cargar la relación 'template' para incluir la información en la respuesta
        $favorite->load(['template' => function($query) {
            $query->with('media'); 
        }]);

        // Formatear la respuesta para incluir las URLs de audio e icono
        $formattedFavorite = [
            'id' => $favorite->id,
            'template_id' => $favorite->template_id,
            'template' => [
                'id' => $favorite->template->id,
                'name' => $favorite->template->name,
                'description' => $favorite->template->description,
                'audio_url' => $favorite->template->audio_url,
                'icon_url' => $favorite->template->icon_url,  
            ],
        ];

        return response()->json($formattedFavorite, 201); // 201 creación exitosa
    }

    /**
     * Elimina un template de la lista de favoritos del usuario autenticado.
     */
    public function destroy(string $id)
    {
        // Buscar el favorito por ID y verificar que pertenezca al usuario autenticado
        $favorite = Favorite::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Eliminar el favorito
        $favorite->delete();

        return response()->json(null, 204); // 204 No Content (eliminación exitosa)
    }
}
