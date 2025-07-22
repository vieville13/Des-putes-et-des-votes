
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte de France - Circonscriptions</title>
    <link rel="stylesheet" href="assets/css/carte.css">
</head>
<body>
    <div class="carte-container">
        <div class="carte-header">
            <h1>🗺️ Carte des circonscriptions</h1>
            <p>Trouvez votre député par circonscription</p>
        </div>

        <div class="carte-controls">
            <div class="search-section">
                <input type="text" id="searchInput" placeholder="Rechercher par département, région ou député..." class="search-input">
                <button id="searchBtn" class="search-button">Rechercher</button>
            </div>
            
            <div class="filter-section">
                <select id="regionFilter" class="filter-select">
                    <option value="">Toutes les régions</option>
                </select>
                <select id="departementFilter" class="filter-select">
                    <option value="">Tous les départements</option>
                </select>
            </div>
        </div>

        <div class="carte-content">
            <div class="carte-frame">
                <div id="carteInteractive" class="carte-interactive">
                    <!-- La carte sera générée ici -->
                </div>
            </div>
            
            <div class="carte-sidebar">
                <div id="infoPanel" class="info-panel">
                    <h3>Informations</h3>
                    <p>Cliquez sur un département pour voir ses circonscriptions</p>
                </div>
                
                <div id="resultsPanel" class="results-panel">
                    <h3>Résultats</h3>
                    <div id="resultsList" class="results-list">
                        <!-- Les résultats apparaîtront ici -->
                    </div>
                </div>
            </div>
        </div>

        <div class="stats-section">
            <h3>Statistiques</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number"><?= count($circonscriptions) ?></span>
                    <span class="stat-label">Circonscriptions</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= count($departementsStats) ?></span>
                    <span class="stat-label">Départements</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number"><?= count(array_unique(array_column($circonscriptions, 'region'))) ?></span>
                    <span class="stat-label">Régions</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Données des circonscriptions
        window.circonscriptionsData = <?= json_encode(array_values($circonscriptions)) ?>;
        window.departementsStats = <?= json_encode($departementsStats) ?>;
    </script>
    <script src="assets/js/carte.js"></script>
</body>
</html>
