<?php
// php/check-url.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['url'])) {
    echo json_encode(['exists' => false, 'error' => 'No URL provided']);
    exit;
}

$url = $_GET['url'];

// Sécurité : On ne vérifie que des liens CurseForge
if (strpos($url, 'curseforge.com') === false) {
    echo json_encode(['exists' => false, 'error' => 'Not a valid domain']);
    exit;
}

// Initialisation de cURL avec la méthode HEAD (pour lire uniquement les en-têtes)
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// CurseForge renvoie 200 si la page existe, 404 sinon.
echo json_encode(['exists' => ($httpCode === 200)]);
?>