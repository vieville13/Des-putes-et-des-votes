
<?php
echo "=== ANALYSE DU DÉPUTÉ PA569 ===\n\n";

$file = 'attached_assets/json/acteur/PA795318.json';
if (!file_exists($file)) {
    die("❌ Fichier non trouvé : $file\n");
}

$data = json_decode(file_get_contents($file), true);
if (!$data || !isset($data['acteur'])) {
    die("❌ Format JSON invalide\n");
}
var_dump($data);

$acteur = $data['acteur'];

// Informations personnelles
echo "👤 INFORMATIONS PERSONNELLES\n";
echo "────────────────────────────\n";
$etatCivil = $acteur['etatCivil'];
echo "Nom complet : " . $etatCivil['ident']['prenom'] . " " . $etatCivil['ident']['nom'] . "\n";
echo "Civilité : " . $etatCivil['ident']['civ'] . "\n";
echo "Trigramme : " . $etatCivil['ident']['trigramme'] . "\n";
echo "Date de naissance : " . $etatCivil['infoNaissance']['dateNais'] . "\n";
echo "Lieu de naissance : " . $etatCivil['infoNaissance']['villeNais'] . " (" . $etatCivil['infoNaissance']['depNais'] . ")\n";
echo "Pays de naissance : " . $etatCivil['infoNaissance']['paysNais'] . "\n\n";

// Profession
echo "💼 PROFESSION\n";
echo "─────────────\n";
echo "Profession : " . $acteur['profession']['libelleCourant'] . "\n";
echo "Catégorie INSEE : " . $acteur['profession']['socProcINSEE']['catSocPro'] . "\n";
echo "Famille socio-professionnelle : " . $acteur['profession']['socProcINSEE']['famSocPro'] . "\n\n";

// Adresses et contacts
echo "📧 CONTACTS\n";
echo "───────────\n";
foreach ($acteur['adresses']['adresse'] as $adresse) {
    echo "- " . $adresse['typeLibelle'] . " : ";
    if ($adresse['@xsi:type'] === 'AdressePostale_Type') {
        echo ($adresse['intitule'] ?? '') . " " . ($adresse['numeroRue'] ?? '') . " " . ($adresse['nomRue'] ?? '') . " " . ($adresse['codePostal'] ?? '') . " " . ($adresse['ville'] ?? '');
    } else {
        echo $adresse['valElec'] ?? 'Non défini';
    }
    echo "\n";
}
echo "\n";

// Mandats
echo "🏛️ MANDATS ET FONCTIONS\n";
echo "───────────────────────\n";
$mandats = $acteur['mandats']['mandat'];
echo "Nombre total de mandats : " . count($mandats) . "\n\n";

// Grouper les mandats par type
$mandatsParType = [];
foreach ($mandats as $mandat) {
    $type = $mandat['typeOrgane'];
    if (!isset($mandatsParType[$type])) {
        $mandatsParType[$type] = [];
    }
    $mandatsParType[$type][] = $mandat;
}

foreach ($mandatsParType as $type => $mandatsType) {
    switch ($type) {
        case 'ASSEMBLEE':
            echo "📍 MANDAT PARLEMENTAIRE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Assemblée Nationale (17ème législature)\n";
                echo "  - Circonscription : " . $mandat['election']['lieu']['departement'] . " (" . $mandat['election']['lieu']['numDepartement'] . ") - " . $mandat['election']['lieu']['numCirco'] . "ème\n";
                echo "  - Région : " . $mandat['election']['lieu']['region'] . "\n";
                echo "  - Prise de fonction : " . $mandat['mandature']['datePriseFonction'] . "\n";
                echo "  - Place hémicycle : " . $mandat['mandature']['placeHemicycle'] . "\n";
                echo "  - Première élection : " . ($mandat['mandature']['premiereElection'] === '1' ? 'Oui' : 'Non') . "\n";
                
                if (isset($mandat['collaborateurs']['collaborateur'])) {
                    echo "  - Collaborateurs (" . count($mandat['collaborateurs']['collaborateur']) . ") :\n";
                    foreach ($mandat['collaborateurs']['collaborateur'] as $collab) {
                        echo "    • " . $collab['qualite'] . " " . $collab['prenom'] . " " . $collab['nom'] . "\n";
                    }
                }
            }
            break;
            
        case 'GP':
            echo "\n🏷️ GROUPE POLITIQUE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Statut : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'COMPER':
            echo "\n📋 COMMISSION PERMANENTE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'PARPOL':
            echo "\n🎯 PARTI POLITIQUE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'GE':
            echo "\n👥 GROUPES D'ÉTUDE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'GA':
            echo "\n🤝 GROUPES D'AMITIÉ (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'ORGEXTPARL':
            echo "\n🌍 ORGANISMES EXTRAPARLEMENTAIRES (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'GEVI':
            echo "\n🔍 GROUPE D'ÉVALUATION ET DE CONTRÔLE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        case 'MISINFOPRE':
            echo "\n📊 MISSION D'INFORMATION PRÉSIDENTIELLE (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
            break;
            
        default:
            echo "\n❓ AUTRES MANDATS ($type) (" . count($mandatsType) . ")\n";
            foreach ($mandatsType as $mandat) {
                echo "  - Qualité : " . $mandat['infosQualite']['libQualite'] . "\n";
                echo "  - Depuis : " . $mandat['dateDebut'] . "\n";
            }
    }
}

echo "\n📊 RÉSUMÉ STATISTIQUE\n";
echo "────────────────────\n";
echo "Total des responsabilités : " . count($mandats) . "\n";
echo "Types d'organes : " . count($mandatsParType) . "\n";

// Mandats actifs
$mandatsActifs = 0;
foreach ($mandats as $mandat) {
    if ($mandat['dateFin'] === null) {
        $mandatsActifs++;
    }
}
echo "Mandats actifs : $mandatsActifs\n";

echo "\n🔗 LIENS OFFICIELS\n";
echo "─────────────────\n";
echo "HATVP : " . $acteur['uri_hatvp'] . "\n";

echo "\n═══════════════════════════════════════\n";
echo "Ce député est très actif avec de nombreuses\n";
echo "responsabilités dans différents organes.\n";
echo "═══════════════════════════════════════\n";
?>
