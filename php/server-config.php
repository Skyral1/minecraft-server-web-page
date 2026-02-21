<?php
// server-config.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Trouver le bon chemin vers l'autoload, peu importe d'où on l'appelle
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$envPath = __DIR__ . '/../';

require $autoloadPath;
use Dotenv\Dotenv;

if (file_exists($envPath . '.env')) {
    Dotenv::createImmutable($envPath)->load();
}

// Récupération des identifiants depuis le .env
$sftpHost = $_ENV['SFTP_HOST'] ?? null;
$sftpPort = $_ENV['SFTP_PORT'] ?? '2022';
$sftpUser = $_ENV['SFTP_USER'] ?? null;
$sftpPass = $_ENV['SFTP_PASS'] ?? null;

if (!$sftpHost || !$sftpUser || !$sftpPass) {
    echo json_encode(["error" => "Identifiants SFTP manquants dans le .env"]);
    exit;
}

// 1. URL SFTP pour le fichier avec cURL
// Par défaut, le chemin sur Pterodactyl/MineStrator est à la racine de l'utilisateur FTP
$sftpUrl = "sftp://{$sftpHost}:{$sftpPort}/server.properties";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sftpUrl);
curl_setopt($ch, CURLOPT_USERPWD, "{$sftpUser}:{$sftpPass}");
// curl_setopt($ch, CURLOPT_SSH_AUTH_TYPES, CURLSSH_AUTH_PASSWORD);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout court pour ne pas bloquer

// Désactiver la vérification d'hôte SSH (souvent nécessaire sur les hébergeurs)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$fileContent = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($fileContent === false || empty($fileContent)) {
    echo json_encode([
        "error" => "Impossible de télécharger le server.properties via SFTP.",
        "curl_error" => $error
    ]);
    exit;
}

// 2. On parse le texte brut du server.properties
$lines = explode("\n", str_replace("\r", "", $fileContent));
$properties = [];

foreach ($lines as $line) {
    $line = trim($line);
    // On ignore les commentaires et les lignes vides
    if (empty($line) || strpos($line, '#') === 0)
        continue;

    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $properties[trim($parts[0])] = trim($parts[1]);
    }
}

// 3. Traduction pour l'affichage en français propre
$finalConfig = [
    // La première lettre en majuscule, ex: survival -> Survival
    "Mode de jeu" => ucfirst($properties['gamemode'] ?? "Inconnu"),
    "Difficulté" => ucfirst($properties['difficulty'] ?? "Inconnue"),
    "Whitelist" => ($properties['white-list'] ?? "false") === "true" ? "Activée" : "Désactivée",
    "Crackés" => ($properties['online-mode'] ?? "true") === "true" ? "Refusés" : "Acceptés",
    "Vue (Chunks)" => ($properties['view-distance'] ?? "10") . " chunks",
    "Port" => $properties['server-port'] ?? "25565"
];

// 4. (Bonus optionnel) Si tu veux ajouter le nom et IP en dur
$finalConfig["Jeu"] = "Minecraft Forge"; // Modifié selon ta config
$finalConfig["IP"] = "bmc4.strator.gg";

// On renvoie le résultat JSON au Javascript
echo json_encode([
    "success" => true,
    "config" => $finalConfig
]);
