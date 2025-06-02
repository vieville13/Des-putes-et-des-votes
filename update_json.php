<?php
// Nouvelle URL du fichier JSON des députés en exercice
$url = 'https://www.nosdeputes.fr/deputes/enmandat/json';

// Emplacement local pour enregistrer le fichier
$destination = __DIR__ . '/data/deputes-en-exercice.json';

// Créer le dossier /data s'il n'existe pas
if (!file_exists(dirname($destination))) {
    mkdir(dirname($destination), 0777, true);
}

// Télécharger le contenu de la page
$pageContent = file_get_contents($url);

if ($pageContent === false) {
    die("❌ Échec du téléchargement depuis $url\n");
}

// Extraire l'URL du fichier JSON à partir du contenu de la page
preg_match('/href="(.*?\.json)"/', $pageContent, $matches);

if (!isset($matches[1])) {
    die("❌ Impossible de trouver le lien vers le fichier JSON sur la page.\n");
}

$jsonFileUrl = $matches[1];

// Télécharger le fichier JSON
$jsonData = file_get_contents($jsonFileUrl);

if ($jsonData === false) {
    die("❌ Échec du téléchargement du fichier JSON depuis $jsonFileUrl\n");
}

// Enregistrer le fichier localement
file_put_contents($destination, $jsonData);

echo "✅ Fichier JSON mis à jour avec succès dans : $destination\n";