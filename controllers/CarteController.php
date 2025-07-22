<?php
require_once __DIR__ . '/../classes/CarteManager.php';

class CarteController {
    private $carteManager;

    public function __construct() {
        $this->carteManager = new CarteManager();
    }

    public function showCarte() {
        $regions = $this->carteManager->getAllRegionsWithCirconscriptions();

        require __DIR__ . '/../views/carte_view.php';
    }

    public function getCirconscriptionInfo($codeCirco) {
        header('Content-Type: application/json');

        $result = $this->carteManager->getCirconscriptionWithDepute($codeCirco);

        if (!$result) {
            http_response_code(404);
            echo json_encode(['error' => 'Circonscription non trouvée']);
            return;
        }

        $response = [
            'circonscription' => [
                'code' => $result['circonscription']->code,
                'departement' => $result['circonscription']->getDepartementNom(),
                'numero' => $result['circonscription']->numero
            ],
            'depute' => null
        ];

        if ($result['depute']) {
            $response['depute'] = [
                'uid' => $result['depute']->uid,
                'nom' => $result['depute']->getNomComplet(),
                'profession' => $result['depute']->profession,
                'url' => '?slug=' . $result['depute']->uid
            ];
        }

        echo json_encode($response);
    }

    public function searchByCommune($commune) {
        // Recherche par commune (nécessiterait une base de données ou API)
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Fonction non implémentée']);
    }
}