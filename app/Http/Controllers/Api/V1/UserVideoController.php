<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class UserVideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the user's videos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $videos = UserVideo::where('user_id', Auth::id())
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($video) {
                return $this->formatVideoResponse($video);
            });

        return response()->json($videos);
    }

    /**
     * Muestra los detalles de un video específico (para API).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $video = UserVideo::where('user_id', Auth::id())
            ->where('id', $id)
            ->with('template')
            ->firstOrFail();

        return response()->json($this->formatVideoResponse($video));
    }

    /**
     * Muestra una vista de previsualización del video.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function preview($id)
    {
        $video = UserVideo::with('template')->findOrFail($id);
        
        return view('user-videos.preview', compact('video'));
    }

    /**
     * Remove the specified user video.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $video = UserVideo::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Eliminar el archivo de video
        Storage::delete('public/' . $video->file_path);

        // Eliminar el registro de la base de datos
        $video->delete();

        return response()->json(['message' => 'Video deleted successfully']);
    }

    /**
     * Download the specified user video.
     *
     * @param  int  $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadVideo($id)
    {
        $video = UserVideo::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
            
        $path = storage_path('app/public/' . $video->file_path);
        $fileName = basename($path);

        return response()->download($path, $fileName, [
            'Content-Type' => 'video/mp4',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }

    /**
     * Format video response with additional data needed by the frontend
     *
     * @param  UserVideo  $video
     * @return array
     */
    protected function formatVideoResponse($video)
    {
        // Obtener nombre del archivo sin extensión para usar como nombre
        $fileName = pathinfo($video->file_path, PATHINFO_FILENAME);
        
        return [
            'id' => $video->id,
            'name' => "Video #" . $video->id,  
            'created_at' => $video->created_at,
            'updated_at' => $video->updated_at,
            'file_path' => $video->file_path,
            'video_url' => URL::to('/storage/' . $video->file_path),
            'template' => $video->template,
            // Puedes agregar un thumbnail_url si lo tienes disponible
            // 'thumbnail_url' => URL::to('/storage/thumbnails/' . $fileName . '.jpg'),
        ];
    }
}