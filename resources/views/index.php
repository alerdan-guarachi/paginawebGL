<?php

include 'apigoogledrive/vendor/autoload.php';

putenv('GOOGLE_APLICATION_CREDENTIALS=config/credentials/pruebasistemas-422621-20697139946e.json');

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->SetScopes(['https://www.googleapis.com/auth/drive.file']);

try{
    $service = new Google_Service_Drive($client);
    $file_path = "logo.png";

    $file = new Google_Service_Drive_DriveFile();
    $file->setName($file_path);

    $file->setParents(array("1Qxstam_8qTvBz98Wydw5L0MLY5mcIveV"));
    $file->setDescription("Archivo cargado desde PHP");
    $file->setMimeType("image/png");

    $resultado = $service->files->create(
        $file,
        array(
            'data' => file_get_contents($file_path),
            'mimeType' => "image/png",
            'uploadType' => 'media',
        )
    );
    echo '<a> href="https://drive.google.com/open?id='. $resultado->id . '" target="_blanck">'. $resultado->name.'</a>';
}catch(Google_Service_Exception $gs){
    $mensaje = json_decode($gs->getMessage());
    echo $mensaje->error->message();
} catch (Exception $e){
    echo $e->getMessage();
}