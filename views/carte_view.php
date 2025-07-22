
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte des circonscriptions - Trouvez votre député</title>
    <link rel="stylesheet" href="assets/css/carte.css">
</head>
<body>
    <div class="header">
        <h1>🗺️ Trouvez votre député</h1>
        <p>Cliquez sur un département pour voir ses circonscriptions</p>
    </div>

    <div class="search-section">
        <div class="search-box">
            <input type="text" id="search-commune" placeholder="Rechercher par commune...">
            <button id="search-btn">🔍 Rechercher</button>
        </div>
    </div>

    <div class="carte-container">
        <div class="regions-grid">
            <?php foreach ($regions as $depCode => $region): ?>
                <div class="region-card" data-departement="<?= htmlspecialchars($depCode) ?>">
                    <h3><?= htmlspecialchars($region['nom']) ?> (<?= htmlspecialchars($depCode) ?>)</h3>
                    <div class="circonscriptions-list">
                        <?php foreach ($region['circonscriptions'] as $circo): ?>
                            <div class="circonscription-item" data-code="<?= htmlspecialchars($circo->code) ?>">
                                Circonscription <?= htmlspecialchars($circo->numero) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal pour afficher les infos du député -->
    <div id="depute-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <div class="footer">
        <a href="index.php">← Retour à la recherche</a>
    </div>

    <script src="assets/js/carte.js"></script>
</body>
</html>
