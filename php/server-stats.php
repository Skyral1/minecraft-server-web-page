<?php
// server-stats.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Trouver le bon chemin vers l'autoload
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$envPath = __DIR__ . '/../';

require $autoloadPath;
use Dotenv\Dotenv;
use WebSocket\Client;

if (file_exists($envPath . '.env')) {
    Dotenv::createImmutable($envPath)->load();
}


$apiKey = $_ENV['MINESTRATOR_API_TOKEN'] ?? null;
$serverId = $_ENV['MINESTRATOR_SERVER_ID'] ?? null;
$apiUrl = $_ENV['MINESTRATOR_API_URL'] ?? "https://mine.sttr.io/server";

if (!$apiKey || !$serverId) {
    echo json_encode(["error" => "Configuration manquante"]);
    exit;
}

// 1. Appel HTTP pour obtenir l'URL WebSocket et le Token
$ch = curl_init($apiUrl . "/" . $serverId);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER => [
        "authorization: Bearer " . trim($apiKey),
        "origin: https://minestrator.com"
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0
]);

$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

if (!isset($data['api']['data']['websocket'])) {
    echo json_encode(["error" => "Impossible d'obtenir les infos WebSocket", "details" => $data]);
    exit;
}

$wsUrl = $data['api']['data']['websocket']['url'];
$wsToken = $data['api']['data']['websocket']['token'];
$ramMaxBytes = $data['api']['data']['mybox']['resources']['ram'] * 1024 * 1024 * 1024;
$diskMaxBytes = ($data['api']['data']['mybox']['resources']['disk'] ?? 80) * 1024 * 1024 * 1024;

// 2. Connexion WebSocket DEPUIS PHP
try {
    $client = new Client($wsUrl, [
        'timeout' => 5,
        'headers' => [
            'Origin' => 'https://minestrator.com',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]);

    $authPayload = json_encode(['event' => 'auth', 'args' => [$wsToken]]);
    $client->send($authPayload);

    $serverStats = null;
    $serverStatus = "unknown";

    for ($i = 0; $i < 5; $i++) {
        $message = $client->receive();
        if (!$message)
            continue;

        $msgData = json_decode($message, true);

        if (isset($msgData['event']) && $msgData['event'] === 'status') {
            $serverStatus = $msgData['args'][0] ?? "unknown";
        }
        if (isset($msgData['event']) && $msgData['event'] === 'stats') {
            $serverStats = json_decode($msgData['args'][0], true);
            break;
        }
    }
    $client->close();

    // 3. Formatage de la réponse
    if ($serverStats) {
        echo json_encode([
            "success" => true,
            "status" => $serverStatus,
            "cpu" => $serverStats['cpu_absolute'] ?? 0,
            "ram_used_bytes" => $serverStats['memory_bytes'] ?? 0,
            "ram_max_bytes" => $ramMaxBytes,
            "disk_bytes" => $serverStats['disk_bytes'] ?? 0,
            "disk_max_bytes" => $diskMaxBytes
        ]);
    } else {
        echo json_encode(["error" => "Serveur éteint ou stats non reçues à temps", "status" => $serverStatus]);
    }
} catch (\Exception $e) {
    echo json_encode(["error" => "Erreur WebSocket PHP", "message" => $e->getMessage()]);
}
?>