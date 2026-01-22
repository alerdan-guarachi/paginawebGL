<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('TU_CLIENT_ID');
$client->setClientSecret('TU_CLIENT_SECRET');
$client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob'); // Para apps de escritorio
$client->addScope(Google_Service_Drive::DRIVE);

$authUrl = $client->createAuthUrl();
echo "1) Abre esta URL en tu navegador:\n$authUrl\n\n";

echo "2) Copia el código de autorización que te da Google y pégalo aquí: ";
$handle = fopen("php://stdin","r");
$code = trim(fgets($handle));

$accessToken = $client->fetchAccessTokenWithAuthCode($code);

echo "\nTu nuevo Refresh Token es:\n";
echo $accessToken['refresh_token'] . "\n";
