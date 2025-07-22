
<?php
require_once __DIR__ . '/../classes/CarteManager.php';
require_once __DIR__ . '/../classes/Depute.php';

class CarteController {
    private $carteManager;
    private $deputeController;

    public function __construct() {
        $this->carteManager = new CarteManager();
    }

    public function setDeputeController($deputeController): void {
        $this->deputeController = $deputeController;
    }

    public function showCarte(): void {
        $circonscriptions = $this->carteManager->getAllCirconscriptions();
        $departementsStats = $this->carteManager->getDepartementsStats();
        
        require __DIR__ . '/../views/carte_view.php';
    }

    public function getCirconscriptionsJson(): string {
        header('Content-Type: application/json');
        return json_encode($this->carteManager->getAllCirconscriptions());
    }

    public function searchCirconscriptions(string $query): array {
        return $this->carteManager->searchCirconscriptions($query);
    }

    public function getCirconscriptionsByDepartement(string $numDepartement): array {
        return $this->carteManager->getCirconscriptionsByDepartement($numDepartement);
    }

    public function handleAjaxRequest(): void {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'circonscriptions':
                echo $this->getCirconscriptionsJson();
                break;
            case 'departement':
                $numDep = $_GET['dep'] ?? '';
                if ($numDep) {
                    $circonscriptions = $this->getCirconscriptionsByDepartement($numDep);
                    header('Content-Type: application/json');
                    echo json_encode($circonscriptions);
                }
                break;
            case 'search':
                $query = $_GET['q'] ?? '';
                if ($query) {
                    $results = $this->searchCirconscriptions($query);
                    header('Content-Type: application/json');
                    echo json_encode($results);
                }
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Action non reconnue']);
        }
    }
}
