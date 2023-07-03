<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Str;
use ZipArchive;

trait UploadableFile
{
    /**
     * Returns one file path
     * @param $file
     * @param $folder
     * @param $disk
     * @param $filename
     *
     * @return mixed
     */
    public function uploadOne($file, $folder = null, $disk = 'public', $filename = null): string
    {
        // $filename = Str::uuid() . '_' . $filename;
        $filename = uniqid() . '_' . $filename;
        return $file->storeAs(
            $folder,
            $filename,
            $disk,
        );
    }


    /**
     * Returns multiple file paths
     * @param $files
     * @param $folder
     * @param $disk
     *
     * @return array
     */
    public function uploadMany($files, $folder = null, $disk = 'public'): array
    {
        foreach ($files as $file) {
            $filename = Str::uuid() . '_' . $file->getClientOriginalName();
            $file->storeAs($folder, $filename, [
                'disk' => $disk,
            ]);
            $data[] = $filename;
        }

        return $data;
    }

    public function downloadFilesZip($folderPath = '/')
    {
        // Obtén los archivos que deseas descargar desde S3
        // $folderPath = Storage::path('files/expedients/id_' . $document->expedient_id . "/id_" . $document->id);
        $zip = new ZipArchive();

        // Establece el nombre y la ubicación del archivo ZIP temporal
        $zipFileName = 'archivos.zip';
        $zipFilePath = storage_path('app/' . $zipFileName);

        // Abre el archivo ZIP para escribir
        $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // Obtiene el adaptador de S3 configurado en Laravel

        // Obtiene el Cliente y  Nombre del bucket
        $client = Storage::disk('s3')->getAdapter()->getClient();
        $bucketName = Storage::disk('s3')->getAdapter()->getBucket();
        // Configura el adaptador de AWS S3 para Flysystem
        $s3Adapter = new AwsS3Adapter($client, $bucketName);

        // Configura el sistema de archivos para Flysystem
        $filesystem = new Filesystem($s3Adapter);

        // Obtiene los archivos dentro de la carpeta en S3
        $files = $filesystem->listContents($folderPath, false);

        // Agrega los archivos al archivo ZIP
        foreach ($files as $file) {
            if ($file['type'] === 'file') {
                // Obtiene el contenido del archivo desde S3
                $fileContent = $filesystem->read($file['path']);

                // Obtiene el nombre del archivo sin la ruta completa
                $fileName = basename($file['path']);

                // Agrega el archivo al archivo ZIP
                $zip->addFromString($fileName, $fileContent);
            }
        }

        // Cierra el archivo ZIP
        $zip->close();

        // Descarga el archivo ZIP
        return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);

    }
}