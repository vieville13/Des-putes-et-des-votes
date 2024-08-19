<?php
// Initialiser cURL
$ch = curl_init();

// URL de l'API
$url = 'https://www.nosdeputes.fr/deputes/xml';

// Configurer cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Ajouter des en-têtes HTTP si nécessaire


// Exécuter la requête cURL
$response = curl_exec($ch);
$xml=simplexml_load_string($response);
// Vérifier si la requête a réussi
if ($response === FALSE) {
    die('Erreur : ' . curl_error($ch));
}

// Fermer la session cURL
curl_close($ch);

// Traiter la réponse
$deputes=$xml->xpath('//depute');
foreach ($deputes as $depute) {
   if( $depute->nom_de_famille == "Bazin" ) {
       echo $depute->twitter;
   }
}
?>