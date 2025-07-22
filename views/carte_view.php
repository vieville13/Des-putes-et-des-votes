
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte des circonscriptions législatives</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <link rel="stylesheet" href="assets/css/carte.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2em;
        }
        
        .back-link {
            color: #ecf0f1;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        #map {
            height: 80vh;
            width: 100%;
        }
        
        .circonscription {
            fill: #3498db;
            fill-opacity: 0.3;
            stroke: #2980b9;
            stroke-width: 2;
            cursor: pointer;
        }
        
        .circonscription:hover {
            fill: #e74c3c;
            fill-opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="back-link">← Retour à l'accueil</a>
        <h1>🗺️ Carte des circonscriptions législatives</h1>
        <p>Cliquez sur une circonscription pour voir les informations du député</p>
    </div>

    <div id="map"></div>

    <!-- Modal pour afficher les informations -->
    <div id="depute-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Passer les données PHP vers JavaScript
        const circonscriptions = <?= json_encode(array_values($regions)) ?>;
    </script>
    
    <script src="assets/js/carte.js"></script>
</body>
</html>
