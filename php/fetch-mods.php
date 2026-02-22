<?php
// php/fetch-mods.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$envPath = __DIR__ . '/../';

require $autoloadPath;
use Dotenv\Dotenv;

if (file_exists($envPath . '.env')) {
    Dotenv::createImmutable($envPath)->load();
}

$sftpHost = $_ENV['SFTP_HOST'] ?? null;
$sftpPort = $_ENV['SFTP_PORT'] ?? '2022';
$sftpUser = $_ENV['SFTP_USER'] ?? null;
$sftpPass = $_ENV['SFTP_PASS'] ?? null;
$discordWebhook = $_ENV['DISCORD_WEBHOOK_URL'] ?? null;

if (!$sftpHost || !$sftpUser || !$sftpPass) {
    echo json_encode(["error" => "Identifiants SFTP manquants"]);
    exit;
}

$cacheFile = __DIR__ . '/mods_cache.json';
$cacheTime = 3600; // Cache d'une heure

// Si le cache est valide, on l'utilise
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    $cachedData = json_decode(file_get_contents($cacheFile), true);
    if ($cachedData) {
        echo json_encode($cachedData);
        exit;
    }
}

// SINON : Connexion SFTP
$sftpUrl = "sftp://{$sftpHost}:{$sftpPort}/mods/";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sftpUrl);
curl_setopt($ch, CURLOPT_USERPWD, "{$sftpUser}:{$sftpPass}");
curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_DIRLISTONLY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$fileList = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($fileList === false) {
    if (file_exists($cacheFile)) {
        echo file_get_contents($cacheFile);
        exit;
    }
    echo json_encode(["error" => "Impossible de lister les mods.", "curl_error" => $error]);
    exit;
}

$files = explode("\n", trim($fileList));
$modsList = [];
$currentModFiles = [];

foreach ($files as $file) {
    $file = trim($file);
    if (empty($file))
        continue;

    if (pathinfo($file, PATHINFO_EXTENSION) === 'jar') {
        $currentModFiles[] = $file;

        $cleanName = preg_replace('/(-mc\d+\.\d+(\.\d+)?.*|\d+\.\d+.*)\.jar$/i', '', $file);
        $cleanName = str_replace('.jar', '', $cleanName);
        $cleanName = str_replace(['-', '_'], ' ', $cleanName);
        $cleanName = ucwords($cleanName);

        $modsList[] = [
            "filename" => $file,
            "name" => $cleanName
        ];
    }
}

usort($modsList, function ($a, $b) {
    return strcasecmp($a['name'], $b['name']);
});

$output = [
    "success" => true,
    "last_updated" => date('d/m/Y H:i:s'),
    "mods" => $modsList
];

// ==========================================
// FONCTION API MODRINTH
// ==========================================
function getModrinthDownloadUrl($filename) {
    // 1. Extraire le nom du mod sans la version pour la recherche
    $query = preg_replace('/\.jar$/i', '', $filename);
    if (preg_match('/^([a-zA-Z0-9_\-]+?)(?:-[0-9]|-[vV]?[0-9]|-mc[0-9]|\+[0-9])/', $filename, $matches)) {
        $query = $matches[1];
    }
    $query = str_replace(['_', '-'], ' ', $query);

    // 2. Chercher le projet sur Modrinth (on prend le 1er résultat)
    $searchUrl = "https://api.modrinth.com/v2/search?query=" . urlencode($query) . "&limit=1";
    $ch = curl_init($searchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "BMC4-Server-Bot/1.0");
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) return null;
    $data = json_decode($response, true);
    if (empty($data['hits'])) return null;

    $projectId = $data['hits'][0]['project_id'];
    
    // 3. Récupérer toutes les versions de ce projet
    $versionsUrl = "https://api.modrinth.com/v2/project/{$projectId}/version";
    $chV = curl_init($versionsUrl);
    curl_setopt($chV, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chV, CURLOPT_USERAGENT, "BMC4-Server-Bot/1.0");
    curl_setopt($chV, CURLOPT_TIMEOUT, 3);
    $vResponse = curl_exec($chV);
    curl_close($chV);
    
    if ($vResponse) {
        $vData = json_decode($vResponse, true);
        if (is_array($vData)) {
            foreach ($vData as $version) {
                if (isset($version['files'])) {
                    foreach ($version['files'] as $file) {
                        // 4. Si le nom du fichier correspond exactement, on a l'URL CDN !
                        if ($file['filename'] === $filename) {
                            return $file['url'];
                        }
                    }
                }
            }
        }
    }
    return null;
}

// ==========================================
// NOTIFICATION DISCORD (avec anti-doublon)
// ==========================================
if ($discordWebhook && file_exists($cacheFile)) {
    $oldCache = json_decode(file_get_contents($cacheFile), true);

    if (isset($oldCache['mods'])) {
        $oldModFiles = array_column($oldCache['mods'], 'filename');

        $addedMods = array_diff($currentModFiles, $oldModFiles);
        $removedMods = array_diff($oldModFiles, $currentModFiles);

        if (count($addedMods) > 0 || count($removedMods) > 0) {

            // On calcule l'empreinte unique de la liste ACTUELLE
            sort($currentModFiles);
            $currentHash = md5(implode(',', $currentModFiles));

            // On lit le dernier hash notifié
            $hashFile = __DIR__ . '/last_notified_hash.txt';
            $lastNotifiedHash = file_exists($hashFile) ? trim(file_get_contents($hashFile)) : '';

            // On n'envoie QUE si l'état est nouveau
            if ($currentHash !== $lastNotifiedHash) {

                $embedFields = [];

                if (count($addedMods) > 0) {
                    $addedText = "";
                    foreach ($addedMods as $mod) {
                        $downloadUrl = "https://bmc4.minesr.com/mods/" . rawurlencode($mod); // Fallback
                        
                        // Si on ajoute un nombre raisonnable de mods (évite le timeout API)
                        if (count($addedMods) <= 10) {
                            $modrinthUrl = getModrinthDownloadUrl($mod);
                            if ($modrinthUrl) {
                                $downloadUrl = $modrinthUrl;
                            }
                        }
                        
                        $addedText .= "✅ [`{$mod}`]({$downloadUrl})\n";
                    }
                    $embedFields[] = [
                        "name" => "📥 Mods Ajoutés (" . count($addedMods) . ")",
                        "value" => substr($addedText, 0, 1024),
                        "inline" => false
                    ];
                }

                if (count($removedMods) > 0) {
                    $removedText = "";
                    foreach ($removedMods as $mod) {
                        $removedText .= "❌ `{$mod}`\n";
                    }
                    $embedFields[] = [
                        "name" => "🗑️ Mods Retirés (" . count($removedMods) . ")",
                        "value" => substr($removedText, 0, 1024),
                        "inline" => false
                    ];
                }

                $discordPayload = json_encode([
                    "username" => "BMC4 Bot",
                    "avatar_url" => "https://mc-api.net/v3/server/favicon/bmc4.strator.gg",
                    "embeds" => [
                        [
                            "title" => "🔄 Mise à jour des Mods du Serveur !",
                            "color" => 9200359,
                            "description" => "Le serveur a été scanné et des changements ont été détectés dans le dossier `mods/`.\n*Cliquez sur le nom d'un mod ajouté pour le télécharger.*",
                            "fields" => $embedFields,
                            "timestamp" => date("c")
                        ]
                    ]
                ]);

                $chDiscord = curl_init($discordWebhook);
                curl_setopt($chDiscord, CURLOPT_POST, true);
                curl_setopt($chDiscord, CURLOPT_POSTFIELDS, $discordPayload);
                curl_setopt($chDiscord, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($chDiscord, CURLOPT_RETURNTRANSFER, true);
                $discordResponse = curl_exec($chDiscord);
                $discordHttpCode = curl_getinfo($chDiscord, CURLINFO_HTTP_CODE);
                curl_close($chDiscord);

                if ($discordHttpCode === 204) {
                    file_put_contents($hashFile, $currentHash);
                }
            }
        }
    }
}
// ==========================================

file_put_contents($cacheFile, json_encode($output, JSON_PRETTY_PRINT));
echo json_encode($output);
?>