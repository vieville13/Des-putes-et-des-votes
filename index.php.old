<?php
require_once __DIR__ . '/controllers/DeputeController.php';

$controller = new DeputeController();

$slug = $_GET['slug'] ?? '';
$search = $_GET['search'] ?? '';

if ($slug !== '') {
    // Affiche la fiche d'un député
    $controller->show($slug);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de députés</title>
</head>
<body>
    <h1>Recherche de députés</h1>
    <form method="get" action="">
        <input type="text" name="search" placeholder="Prénom, nom ou slug" value="<?= htmlspecialchars($search) ?>" required>
        <button type="submit">Rechercher</button>
    </form>

    <?php if ($search !== ''): ?>
        <?php $results = $controller->searchDeputes($search); ?>
        <?php if (count($results) === 0): ?>
            <p>Aucun député trouvé pour « <?= htmlspecialchars($search) ?> ».</p>
        <?php else: ?>
            <ul>
                <?php foreach ($results as $depute): ?>
                    <li><a href="?slug=<?= htmlspecialchars($depute->slug) ?>">
                        <?= htmlspecialchars($depute->prenom . ' ' . $depute->nom) ?>
                    </a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>