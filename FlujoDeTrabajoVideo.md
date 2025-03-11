Flujo de Trabajo para Generar Videos
1. Creación y Configuración de Plantillas
Seeder de Plantillas:

Las plantillas (Template) se crean utilizando el seeder TemplateSeeder, que inserta información básica como el nombre, descripción, y asocia archivos multimedia (audio e iconos) mediante Spatie Media Library.

Cada plantilla tiene un archivo de audio e icono asociado que se utiliza como base para generar videos.

Modelo Template:

El modelo Template está configurado para manejar colecciones multimedia (audio, icon, y generated_videos) usando Spatie Media Library.

También proporciona métodos para obtener las URLs de los archivos multimedia asociados.

2. Listado de Plantillas
Ruta: GET /v1/auth/templates

Controlador: listTemplates en TemplateController

Función:

Devuelve una lista de todas las plantillas disponibles con su información básica (nombre, descripción) y las URLs de sus archivos multimedia (audio e icono).

Ejemplo de Respuesta:

json
[
    {
        "id": 1,
        "name": "Action Template",
        "description": "For your most adventurous and action videos",
        "audio_url": "http://localhost/storage/audio/action.mp3",
        "icon_url": "http://localhost/storage/icon/adventure.png"
    },
    ...
]
3. Visualización de una Plantilla Específica
Ruta: GET /v1/auth/templates/{id}

Controlador: showTemplate en TemplateController

Función:

Devuelve la información detallada de una plantilla específica, incluyendo las URLs del audio e icono asociados.

Ejemplo de Respuesta:

json
{
    "id": 1,
    "name": "Action Template",
    "description": "For your most adventurous and action videos",
    "audio_url": "http://localhost/storage/audio/action.mp3",
    "icon_url": "http://localhost/storage/icon/adventure.png"
}
4. Generación de Videos
Paso 1: Enviar Archivos e Información del Usuario
Ruta: POST /v1/auth/templates/{id}/generate

Controlador: generateVideo en TemplateController

Datos Esperados:

Imágenes (img1, img2, img3, img4) cargadas por el usuario.

Texto opcional (text1, text2) para personalizar el video.

Validación:

Las imágenes deben ser archivos válidos (jpeg, png, etc.) con un tamaño máximo de 2 MB.

Los textos deben ser cadenas opcionales con un máximo de 10 caracteres.

Paso 2: Procesamiento del Video
El controlador realiza los siguientes pasos:

Valida la solicitud y guarda temporalmente las imágenes cargadas.

Obtiene la plantilla seleccionada (Template) por su ID.

Verifica que exista un archivo de audio asociado a la plantilla.

Obtiene la ruta del archivo de audio desde Spatie Media Library.

Usa la clase VideoTemplateFactory para obtener la lógica específica asociada a la plantilla (por ejemplo, efectos o configuraciones únicas).

La fábrica devuelve una instancia como ActionTemplate, HappyTemplate, etc., según el ID.

Construye el comando necesario para generar el video usando herramientas externas (como FFmpeg).

Ejecuta el comando para generar el video final (almacenado en /storage/app/public/videos).

Si ocurre un error durante el proceso, devuelve un mensaje con los detalles.

Paso 3: Guardar Información del Video Generado
Una vez generado, se guarda un registro en la tabla user_videos con información como:

ID del usuario que generó el video.

ID de la plantilla utilizada.

Ruta del archivo generado.

Ejemplo de Respuesta Exitosa:
json
{
    "message": "Video generated successfully",
    "video_url": "http://localhost/storage/videos/unique_video_id.mp4",
    "user_video_id": 123
}
5. Listado y Gestión de Videos Generados por el Usuario
a) Listar Videos Generados
Ruta: GET /v1/auth/userVideos

Controlador: Método en UserVideoController.

Devuelve una lista de todos los videos generados por el usuario autenticado.

b) Descargar un Video Generado
Ruta: GET /v1/auth/userVideos/{id}/download

Permite al usuario descargar uno de sus videos generados.

c) Eliminar un Video Generado
Ruta: DELETE /v1/auth/userVideos/{id}

Permite al usuario eliminar uno de sus videos generados.

Resumen del Flujo
El usuario consulta las plantillas disponibles (GET /templates).

Selecciona una plantilla específica (GET /templates/{id}).

Carga imágenes y texto para personalizar su video (POST /templates/{id}/generate).

El backend procesa las imágenes y genera el video utilizando la lógica específica asociada a la plantilla.

El video generado se almacena y queda disponible para ser listado, descargado o eliminado.