
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($depute->getNomComplet()) ?> - Député</title>
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
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 2.5em;
        }
        .trigramme {
            background: #3498db;
            color: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            margin: 0 0 20px 0;
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            min-width: 120px;
            margin-right: 15px;
        }
        .info-value {
            flex: 1;
            color: #2c3e50;
        }
        .mandats-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .mandat-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        .mandat-type {
            font-weight: bold;
            color: #2c3e50;
        }
        .mandat-dates {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 5px;
        }
        .collaborateur {
            background: #f8f9fa;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 6px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .external-link {
            color: #3498db;
            text-decoration: none;
        }
        .external-link:hover {
            text-decoration: underline;
        }
        .badge {
            background: #e74c3c;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-link">← Retour à la recherche</a>
    
    <div class="header">
        <h1><?= htmlspecialchars($depute->getNomComplet()) ?></h1>
        <?php if ($depute->trigramme): ?>
            <div class="trigramme"><?= htmlspecialchars($depute->trigramme) ?></div>
        <?php endif; ?>
        <p style="margin: 10px 0 0 0; color: #7f8c8d;">
            <?= $depute->getNombreMandats() ?> mandat(s) actif(s)
        </p>
    </div>

    <div class="grid">
        <!-- Informations personnelles -->
        <div class="card">
            <h2>👤 Informations personnelles</h2>
            <div class="info-row">
                <div class="info-label">Civilité :</div>
                <div class="info-value"><?= htmlspecialchars($depute->sexe) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Naissance :</div>
                <div class="info-value">
                    <?= htmlspecialchars($depute->dateNaissance) ?>
                    <?php if ($depute->lieuNaissance): ?>
                        <br><small><?= htmlspecialchars($depute->lieuNaissance) ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Profession :</div>
                <div class="info-value"><?= htmlspecialchars($depute->profession) ?></div>
            </div>
            <?php if ($depute->email): ?>
            <div class="info-row">
                <div class="info-label">Email :</div>
                <div class="info-value">
                    <a href="mailto:<?= htmlspecialchars($depute->email) ?>" class="external-link">
                        <?= htmlspecialchars($depute->email) ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($depute->uriHatvp): ?>
            <div class="info-row">
                <div class="info-label">HATVP :</div>
                <div class="info-value">
                    <a href="<?= htmlspecialchars($depute->uriHatvp) ?>" target="_blank" class="external-link">
                        Déclaration d'intérêts ↗
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Affiliations politiques -->
        <div class="card">
            <h2>🎯 Affiliations politiques</h2>
            <?php 
            $partiPolitique = null;
            $groupeParlementaire = null;
            
            foreach ($depute->mandats as $mandat) {
                if ($mandat['type'] === 'PARPOL' && !$mandat['dateFin']) {
                    $partiPolitique = $mandat;
                }
                if ($mandat['type'] === 'GP' && !$mandat['dateFin']) {
                    $groupeParlementaire = $mandat;
                }
            }
            ?>
            
            <?php if ($partiPolitique): ?>
            <div class="info-row">
                <div class="info-label">Parti politique :</div>
                <div class="info-value">
                    <strong><?= htmlspecialchars($partiPolitique['qualite']) ?></strong>
                    <span class="badge">Actif</span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($groupeParlementaire): ?>
            <div class="info-row">
                <div class="info-label">Groupe parlementaire :</div>
                <div class="info-value">
                    <strong><?= htmlspecialchars($groupeParlementaire['qualite']) ?></strong>
                    <span class="badge">Actif</span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!$partiPolitique && !$groupeParlementaire): ?>
                <p style="color: #7f8c8d; font-style: italic;">Aucune affiliation politique active trouvée</p>
            <?php endif; ?>
        </div>

        <!-- Mandats -->
        <div class="card">
            <h2>🏛️ Mandats</h2>
            <div class="mandats-list">
                <?php if (empty($depute->mandats)): ?>
                    <p style="color: #7f8c8d; font-style: italic;">Aucun mandat trouvé</p>
                <?php else: ?>
                    <?php foreach ($depute->mandats as $mandat): ?>
                        <div class="mandat-item">
                            <div class="mandat-type">
                                <?= htmlspecialchars($mandat['qualite']) ?>
                                <?php if (!$mandat['dateFin']): ?>
                                    <span class="badge">Actif</span>
                                <?php endif; ?>
                            </div>
                            <div class="mandat-dates">
                                <strong>Début de mandat :</strong> <?= htmlspecialchars($mandat['dateDebut']) ?>
                                <?php if ($mandat['dateFin']): ?>
                                    <br><strong>Fin de mandat :</strong> <?= htmlspecialchars($mandat['dateFin']) ?>
                                <?php endif; ?>
                            </div>
                            <div style="font-size: 0.8em; color: #95a5a6; margin-top: 5px;">
                                Type: <?= htmlspecialchars($mandat['type']) ?>
                                <?php if ($mandat['organe']): ?>
                                    | Organe: <?= htmlspecialchars($mandat['organe']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Collaborateurs -->
        <?php if (!empty($depute->collaborateurs)): ?>
        <div class="card">
            <h2>👥 Collaborateurs</h2>
            <?php foreach ($depute->collaborateurs as $collab): ?>
                <div class="collaborateur">
                    <?= htmlspecialchars($collab['civilite']) ?> 
                    <?= htmlspecialchars($collab['prenom']) ?> 
                    <?= htmlspecialchars($collab['nom']) ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
