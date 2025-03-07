<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Services\Templates\VideoTemplateFactory;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use App\Models\UserVideo;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['listTemplates','showTemplate']]);
    }

    /**
     * Display a listing of the templates.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listTemplates()
    {
        $templates = Template::all();
        return response()->json($templates);
    }

    /**
     * Display the specified template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showTemplate($id)
    {
        $template = Template::findOrFail($id);
        return response()->json($template);
    }

    /**
     * Generate a video using the specified template.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateVideo(Request $request, $id)
    {
        $request->validate([
            'img1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'img2' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'img3' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'img4' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'text1' => 'nullable|string|max:255',
            'text2' => 'nullable|string|max:255',
        ]);

        $template = Template::findOrFail($id);
        $templateFactory = VideoTemplateFactory::make($template->id);

        if (!$templateFactory) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        $params = $this->storeImages($request);
        $params['text1'] = $request->text1 ?? '';
        $params['text2'] = $request->text2 ?? '';

        $outputPath = 'videos/' . uniqid() . '.mp4';
        $fullOutputPath = storage_path('app/public/' . $outputPath);

        $command = $templateFactory->getCommand($params, $fullOutputPath, storage_path('app/audio/' . $template->file_path));

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['error' => 'Error generating video', 'details' => $process->getErrorOutput()], 500);
        }

        $userVideo = UserVideo::create([
            'user_id' => Auth::id(),
            'template_id' => $template->id,
            'file_path' => $outputPath,
        ]);

        return response()->json([
            'message' => 'Video generated successfully',
            'video_url' => Storage::url($outputPath),
            'user_video_id' => $userVideo->id,
        ]);
    }

    /**
     * Store images and return their paths.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function storeImages(Request $request)
    {
        $paths = [];
        for ($i = 1; $i <= 4; $i++) {
            $imagePath = $request->file("img{$i}")->store('public/images');
            $paths["img{$i}"] = Storage::url($imagePath);
        }
        return $paths;
    }
}
