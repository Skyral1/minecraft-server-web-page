<?php
// php/fetch-mods.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$envPath = __DIR__ . '/../';
$cacheFile = __DIR__ . '/../mods_cache.json';

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
    echo file_get_contents($cacheFile);
    exit;
}

require $autoloadPath;
use Dotenv\Dotenv;

if (file_exists($envPath . '.env')) {
    Dotenv::createImmutable($envPath)->load();
}

$sftpHost = $_ENV['SFTP_HOST'] ?? null;
$sftpPort = $_ENV['SFTP_PORT'] ?? '2022';
$sftpUser = $_ENV['SFTP_USER'] ?? null;
$sftpPass = $_ENV['SFTP_PASS'] ?? null;

if (!$sftpHost || !$sftpUser || !$sftpPass) {
    echo json_encode(["error" => "Identifiants SFTP manquants dans le .env"]);
    exit;
}

// 1. URL SFTP vers le dossier cible avec cURL
// Note: Assurez-vous que le dossier se termine par un slash '/'
$sftpUrl = "sftp://{$sftpHost}:{$sftpPort}/mods/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sftpUrl);
curl_setopt($ch, CURLOPT_USERPWD, "{$sftpUser}:{$sftpPass}");
curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Timeout court

// Option vitale pour lister le contenu du dossier plutôt que de le télécharger
curl_setopt($ch, CURLOPT_DIRLISTONLY, true);

// Désactiver la vérification d'hôte SSH (comme dans votre config)
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$dirContent = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($dirContent === false) {
    echo json_encode([
        "error" => "Impossible de lister le dossier /mods via SFTP.",
        "curl_error" => $error
    ]);
    exit;
}

// 2. Parser la liste des fichiers (cURL retourne les noms séparés par des sauts de ligne)
$files = explode("\n", str_replace("\r", "", trim($dirContent)));
$mods = [];

foreach ($files as $file) {
    $file = trim($file);

    // Ignorer les lignes vides, . et ..
    if (empty($file) || $file === '.' || $file === '..') {
        continue;
    }

    // Ne garder que les .jar
    if (pathinfo($file, PATHINFO_EXTENSION) === 'jar') {
        // Nettoyage : Retire la version du mod et l'extension
        // Ex: "jei-1.20.1-forge-15.3.0.4.jar" -> "jei"
        $cleanName = preg_replace('/(-mc\d+\.\d+(\.\d+)?.*|\d+\.\d+.*)\.jar$/i', '', $file);
        $cleanName = str_replace('.jar', '', $cleanName);

        $displayName = ucwords(str_replace(['-', '_'], ' ', $cleanName));

        $mods[] = [
            'filename' => $file,
            'name' => $displayName
        ];
    }
}

// Trier par ordre alphabétique
usort($mods, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

$result = [
    "success" => true,
    "last_updated" => date('Y-m-d H:i:s'),
    "mods" => $mods
];

// 3. Sauvegarder dans le fichier JSON pour la mise en cache
$jsonOutput = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($cacheFile, $jsonOutput);

// Renvoi au format JSON pour Javascript (ou pour inclusion PHP)
echo $jsonOutput;
?>