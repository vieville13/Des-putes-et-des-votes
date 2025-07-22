
<?php
require_once __DIR__ . '/controllers/DeputeController.php';

$controller = new DeputeController();

$uid = $_GET['uid'] ?? '';
$search = $_GET['search'] ?? '';

if ($uid !== '') {
    // Affiche la fiche d'un député
    $controller->show($uid);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de données des députés</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            color: #2c3e50;
            font-size: 3em;
            margin-bottom: 10px;
        }
        .header p {
            color: #7f8c8d;
            font-size: 1.2em;
        }
        .search-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .search-input {
            width: 400px;
            max-width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            margin-right: 10px;
        }
        .search-input:focus {
            outline: none;
            border-color: #3498db;
        }
        .search-button {
            padding: 15px 25px;
            font-size: 16px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
        }
        .search-button:hover {
            background: #2980b9;
        }
        .results {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .results-header {
            background: #3498db;
            color: white;
            padding: 20px;
            font-weight: 600;
            font-size: 1.1em;
        }
        .depute-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1px;
            background: #ecf0f1;
        }
        .depute-card {
            background: white;
            padding: 20px;
            transition: background-color 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .depute-card:hover {
            background: #f8f9fa;
        }
        .depute-name {
            font-weight: 600;
            font-size: 1.1em;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .depute-info {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .no-results {
            padding: 40px;
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
        }
        .stats {
            text-align: center;
            margin-bottom: 20px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏛️ Base de données des députés</h1>
        <p>Consultez les informations complètes des députés français</p>
    </div>

    <form method="get" action="" class="search-form">
        <input type="text" name="search" placeholder="Rechercher par nom, prénom ou identifiant..." 
               value="<?= htmlspecialchars($search) ?>" class="search-input" autofocus>
        <button type="submit" class="search-button">Rechercher</button>
    </form>

    <?php
    if ($search !== '') {
        $results = $controller->searchDeputes($search);
    } else {
        $results = $controller->getAll();
    }
    ?>

    <div class="results">
        <div class="results-header">
            <?php if ($search !== ''): ?>
                Résultats pour « <?= htmlspecialchars($search) ?> » (<?= count($results) ?> trouvé(s))
            <?php else: ?>
                Tous les députés (<?= count($results) ?> au total)
            <?php endif; ?>
        </div>

        <?php if (count($results) === 0): ?>
            <div class="no-results">
                Aucun député trouvé pour votre recherche.
            </div>
        <?php else: ?>
            <div class="depute-grid">
                <?php foreach ($results as $depute): ?>
                    <a href="?uid=<?= htmlspecialchars($depute->uid) ?>" class="depute-card">
                        <div class="depute-name"><?= htmlspecialchars($depute->getNomComplet()) ?></div>
                        <div class="depute-info">
                            <?php if ($depute->trigramme): ?>
                                <?= htmlspecialchars($depute->trigramme) ?> •
                            <?php endif; ?>
                            <?= $depute->getNombreMandats() ?> mandat(s)
                            <?php if ($depute->profession): ?>
                                <br><?= htmlspecialchars($depute->profession) ?>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
