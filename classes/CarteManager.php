
<?php
require_once __DIR__ . '/Circonscription.php';
require_once __DIR__ . '/Depute.php';

class CarteManager {
    private $circonscriptions = [];
    private $deputes = [];

    public function __construct() {
        $this->circonscriptions = Circonscription::getAll();
        $this->loadDeputes();
    }

    private function loadDeputes() {
        $deputesPath = __DIR__ . '/../attached_assets/json/acteur/';
        if (!is_dir($deputesPath)) return;

        $files = glob($deputesPath . '*.json');
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (isset($data['acteur'])) {
                $depute = new Depute($data['acteur']);
                if ($depute->isValid()) {
                    $this->deputes[$depute->uid] = $depute;
                }
            }
        }
    }

    public function getCirconscriptionWithDepute($codeCirco) {
        $circonscription = $this->circonscriptions[$codeCirco] ?? null;
        if (!$circonscription) return null;

        // Chercher le député de cette circonscription
        $depute = $this->findDeputeByCirconscription($codeCirco);

        return [
            'circonscription' => $circonscription,
            'depute' => $depute
        ];
    }

    private function findDeputeByCirconscription($codeCirco) {
        // Convertir le code circonscription en format compatible avec les organes
        foreach ($this->deputes as $depute) {
            foreach ($depute->mandats as $mandat) {
                if ($mandat['type'] === 'ASSEMBLEE' && !$mandat['dateFin']) {
                    // Chercher l'organe correspondant
                    $organeRef = $mandat['organeRef'];
                    if ($this->isCirconscriptionMatch($organeRef, $codeCirco)) {
                        return $depute;
                    }
                }
            }
        }
        return null;
    }

    private function isCirconscriptionMatch($organeRef, $codeCirco) {
        // Charger le fichier organe pour vérifier la correspondance
        $organePath = __DIR__ . '/../attached_assets/json/organe/' . $organeRef . '.json';
        if (!file_exists($organePath)) return false;

        $organeData = json_decode(file_get_contents($organePath), true);
        if (!isset($organeData['organe']['lieu']['departement'])) return false;

        $depCode = $organeData['organe']['lieu']['departement']['code'];
        $numero = $organeData['organe']['numero'] ?? '';
        
        // Format: département + numéro (ex: 69001 pour 69-1)
        $expectedCode = $depCode . str_pad($numero, 2, '0', STR_PAD_LEFT);
        
        return $expectedCode === $codeCirco;
    }

    public function generateSVGMap() {
        $svg = '<svg viewBox="0 0 1000 600" xmlns="http://www.w3.org/2000/svg">';
        
        foreach ($this->circonscriptions as $code => $circo) {
            $points = $circo->convertKMLToSVG();
            if (!empty($points)) {
                $svg .= '<polygon points="' . $points . '" ';
                $svg .= 'class="circonscription" ';
                $svg .= 'data-code="' . htmlspecialchars($code) . '" ';
                $svg .= 'data-nom="' . htmlspecialchars($circo->getDepartementNom() . ' - ' . $circo->numero) . '" ';
                $svg .= '/>';
            }
        }
        
        $svg .= '</svg>';
        return $svg;
    }

    public function getAllRegionsWithCirconscriptions() {
        $regions = [];
        foreach ($this->circonscriptions as $circo) {
            $dep = $circo->departement;
            if (!isset($regions[$dep])) {
                $regions[$dep] = [
                    'nom' => $circo->getDepartementNom(),
                    'circonscriptions' => []
                ];
            }
            $regions[$dep]['circonscriptions'][] = $circo;
        }
        return $regions;
    }
}
