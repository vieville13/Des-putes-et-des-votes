
<?php
require_once __DIR__ . '/controllers/DeputeController.php';
require_once __DIR__ . '/controllers/CarteController.php';

// Gestion des requêtes AJAX pour la carte
if (isset($_GET['action']) && $_GET['action'] === 'getInfo' && isset($_GET['code'])) {
    $carteController = new CarteController();
    $carteController->getCirconscriptionInfo($_GET['code']);
    exit;
}

$controller = new DeputeController();
$carteController = new CarteController();

$slug = $_GET['slug'] ?? '';
$search = $_GET['search'] ?? '';
$showCarte = isset($_GET['carte']);

if ($slug !== '') {
    // Affiche la fiche d'un député
    $controller->show($slug);
    exit;
}

if ($showCarte) {
    // Affiche la carte
    $carteController->showCarte();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assemblée Citoyenne - Trouvez votre député</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .header {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 3em;
            color: #2c3e50;
            font-weight: 300;
        }
        
        .header p {
            margin: 0;
            color: #7f8c8d;
            font-size: 1.2em;
        }
        
        .search-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .method-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .method-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        }
        
        .method-icon {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }
        
        .method-card h2 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 1.5em;
        }
        
        .method-card p {
            color: #7f8c8d;
            margin-bottom: 25px;
        }
        
        .carte-btn {
            display: inline-block;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .carte-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .search-form input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 50px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }
        
        .search-form input:focus {
            border-color: #3498db;
        }
        
        .search-form button {
            padding: 15px 25px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        
        .search-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.4);
        }
        
        .results {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .results ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .results li {
            margin-bottom: 15px;
        }
        
        .results a {
            display: block;
            padding: 15px 20px;
            background: #f8f9fa;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
            border-left: 4px solid #3498db;
        }
        
        .results a:hover {
            background: #3498db;
            color: white;
            transform: translateX(5px);
        }
        
        .no-results {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.2em;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ Assemblée Citoyenne</h1>
            <p>Découvrez qui vous représente à l'Assemblée nationale</p>
        </div>

        <div class="search-methods">
            <div class="method-card">
                <span class="method-icon">🗺️</span>
                <h2>Carte interactive</h2>
                <p>Naviguez par région et circonscription pour trouver votre député facilement</p>
                <a href="?carte=1" class="carte-btn">Ouvrir la carte</a>
            </div>

            <div class="method-card">
                <span class="method-icon">🔍</span>
                <h2>Recherche par nom</h2>
                <p>Recherchez directement un député par son nom, prénom ou identifiant</p>
                
                <form method="get" action="" class="search-form">
                    <input type="text" name="search" placeholder="Prénom, nom ou identifiant..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Rechercher</button>
                </form>
            </div>
        </div>

        <?php if ($search !== ''): ?>
            <div class="results">
                <?php $results = $controller->searchDeputes($search); ?>
                <?php if (count($results) === 0): ?>
                    <div class="no-results">
                        <h3>Aucun député trouvé</h3>
                        <p>Aucun résultat pour « <?= htmlspecialchars($search) ?> »</p>
                        <p>Essayez la carte interactive ou modifiez votre recherche.</p>
                    </div>
                <?php else: ?>
                    <h3>Résultats de la recherche (<?= count($results) ?> député<?= count($results) > 1 ? 's' : '' ?> trouvé<?= count($results) > 1 ? 's' : '' ?>) :</h3>
                    <ul>
                        <?php foreach ($results as $depute): ?>
                            <li>
                                <a href="?slug=<?= htmlspecialchars($depute->uid) ?>">
                                    <strong><?= htmlspecialchars($depute->getNomComplet()) ?></strong>
                                    <?php if ($depute->profession): ?>
                                        <br><small style="opacity: 0.8;"><?= htmlspecialchars($depute->profession) ?></small>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
