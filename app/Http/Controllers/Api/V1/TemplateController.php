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
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class TemplateController extends Controller
{
    public function __construct()
    {
        // Define un middleware para proteger las rutas de la API, excepto las listadas.
        // 'auth:api' verifica que el usuario esté autenticado mediante un token de API.
        $this->middleware('auth:api', ['except' => ['listTemplates', 'showTemplate', 'index', 'store']]);
    }

/**
     * Lista todas las plantillas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $templates = Template::all();

        return response()->json($templates->map(function ($template) {
            return [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'audio_url' => $template->getFirstMediaUrl('audio'), 
                'icon_url' => $template->getFirstMediaUrl('icon'),  
            ];
        }));
    }

    /**
     * Muestra una plantilla específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $template = Template::findOrFail($id);

        return response()->json([
            'id' => $template->id,
            'name' => $template->name,
            'description' => $template->description,
            'audio_url' => $template->getFirstMediaUrl('audio'), 
            'icon_url' => $template->getFirstMediaUrl('icon'),   
        ]);
    }

    /**
     * Crea una nueva plantilla.
     *
     * @param  \Illuminate\Http\Request  $request 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validación de la solicitud
        $request->validate([
            'name' => 'required|string|max:15', 
            'description' => 'nullable|string', 
            'audio' => 'required|file|mimes:mp3,wav,ogg', 
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg', 
        ]);

        // Crear plantilla
        $template = Template::create($request->only('name', 'description'));

        if ($request->hasFile('audio')) {
            $template->addMediaFromRequest('audio')->toMediaCollection('audio');
        }

        if ($request->hasFile('icon')) {
            $template->addMediaFromRequest('icon')->toMediaCollection('icon');
        }

        return response()->json([
            'message' => 'Template created successfully',
            'template' => [
                'id' => $template->id, 
                'name' => $template->name, 
                'description' => $template->description, 
                'audio_url' => $template->getFirstMediaUrl('audio'), 
                'icon_url' => $template->getFirstMediaUrl('icon'), 
            ],
        ], 201);
    }

    /**
     * Genera un video utilizando la plantilla especificada.
     *
     * @param  \Illuminate\Http\Request  $request // Objeto Request que contiene los datos de la solicitud
     * @param  int  $id // ID de la plantilla a utilizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request, $id)
    {
    // Validación de los datos de entrada
    $request->validate([
        'img1' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'img2' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'img3' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'img4' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'text1' => 'nullable|string|max:10',
        'text2' => 'nullable|string|max:10',
    ]);

    // Buscar la plantilla por ID
    $template = Template::findOrFail($id);
    // Crear una instancia de la fábrica de plantillas de video
    $templateFactory = VideoTemplateFactory::make($template->id);

    // Verificar si se encontró la plantilla
    if (!$templateFactory) {
        return response()->json(['error' => 'Template not found'], 404);
    }

    // Almacenar las imágenes y obtener los parámetros
    $params = $this->storeImages($request);
    $params['text1'] = $request->text1 ?? '';
    $params['text2'] = $request->text2 ?? '';

    // Obtener el archivo de audio asociado a la plantilla
    $audio = $template->getFirstMedia('audio');

    // Verificar si se encontró el audio
    if (!$audio) {
        return response()->json(['error' => 'Audio not found for this template'], 404);
    }

    // Construir la ruta completa al archivo de audio
    $audioPath = $audio->getPath();

    // Generar la ruta de salida para el video
    $outputPath = 'videos/' . uniqid() . '.mp4';
    $fullOutputPath = storage_path('app/public/' . $outputPath);

    // Obtener el comando para generar el video
    $command = $templateFactory->getCommand($params, $fullOutputPath, $audioPath);
    // Crear un proceso para ejecutar el comando
    $process = Process::fromShellCommandline($command);
    $process->setTimeout(120);

    // Ejecutar el proceso
    $process->run();

    // Verificar si el proceso se ejecutó correctamente
    if (!$process->isSuccessful()) {
        return response()->json(['error' => 'Error generating video', 'details' => $process->getErrorOutput()], 500);
    }

    // Crear un registro de UserVideo en la base de datos
    $userVideo = UserVideo::create([
        'user_id' => Auth::id(),
        'template_id' => $template->id,
        'file_path' => $outputPath,
    ]);

    // Devolver la respuesta con la información del video generado
    return response()->json([
        'message' => 'Video generated successfully',
        'video_url' => Storage::url($outputPath),
        'user_video_id' => $userVideo->id,
    ]);
}
}