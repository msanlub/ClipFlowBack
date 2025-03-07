<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
    public function listVideo()
    {
        $videos = UserVideo::where('user_id', Auth::id())
            ->with('template')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($videos);
    }

    /**
     * Display the specified user video.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showVideo($id)
{
    $video = UserVideo::where('user_id', Auth::id())
        ->with('template')
        ->findOrFail($id);

    return view('user-videos.preview', compact('video'));
}


    /**
     * Remove the specified user video.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteVideo($id)
    {
        $video = UserVideo::where('user_id', Auth::id())->findOrFail($id);

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
        $video = UserVideo::where('user_id', Auth::id())->findOrFail($id);
        $path = storage_path('app/public/' . $video->file_path);
        $fileName = basename($path);

        return response()->download($path, $fileName, [
            'Content-Type' => 'video/mp4',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }
}
