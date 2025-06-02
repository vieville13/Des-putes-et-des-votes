<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($depute->prenom . ' ' . $depute->nom) ?></title>
</head>
<body>
    <h1><?= htmlspecialchars($depute->prenom . ' ' . $depute->nom) ?></h1>
    <img src="<?= htmlspecialchars($depute->photo) ?>" alt="Photo de <?= htmlspecialchars($depute->prenom) ?>" style="max-width:200px;">
    <p><strong>Département :</strong> <?= htmlspecialchars($depute->departement) ?></p>
    <p><strong>Circonscription :</strong> <?= htmlspecialchars($depute->numCirco) ?></p>
    <p><strong>Groupe :</strong> <?= htmlspecialchars($depute->groupe) ?></p>
    <p><a href="<?= htmlspecialchars($depute->url) ?>" target="_blank" rel="noopener">Voir la fiche complète sur nosdeputes.fr</a></p>
    <p><a href="index.php">← Retour à la recherche</a></p>
</body>
</html>