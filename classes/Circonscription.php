
<?php
class Circonscription {
    public $code;
    public $departement;
    public $numero;
    public $communes;
    public $kmlShape;
    public $editedShape;
    
    private static $circonscriptions = [];
    private static $loaded = false;

    public function __construct($data) {
        $this->code = $data['code_circonscription'] ?? '';
        $this->departement = $data['departement'] ?? '';
        $this->numero = $data['numero'] ?? '';
        $this->communes = !empty($data['communes']) ? explode('-', $data['communes']) : [];
        $this->kmlShape = $data['kml_shape'] ?? '';
        $this->editedShape = ($data['edited_shape'] ?? 'false') === 'true';
    }

    public static function loadFromCSV() {
        if (self::$loaded) return;
        
        $csvFile = __DIR__ . '/../data/circonscriptions.csv';
        if (!file_exists($csvFile)) {
            error_log("Fichier CSV circonscriptions introuvable : " . $csvFile);
            self::$loaded = true;
            return;
        }
        
        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            error_log("Impossible d'ouvrir le fichier CSV");
            self::$loaded = true;
            return;
        }
        
        $headers = fgetcsv($handle);
        if (!$headers) {
            error_log("Impossible de lire les en-têtes du CSV");
            fclose($handle);
            self::$loaded = true;
            return;
        }
        
        // Debug : afficher les en-têtes
        error_log("En-têtes CSV trouvés : " . implode(', ', $headers));
        
        $lineNumber = 1;
        while (($row = fgetcsv($handle)) !== FALSE) {
            $lineNumber++;
            
            // Vérifier que le nombre de colonnes correspond
            if (count($headers) !== count($row)) {
                error_log("Ligne $lineNumber : nombre de colonnes incorrect (" . count($row) . " vs " . count($headers) . ")");
                continue;
            }
            
            $data = array_combine($headers, $row);
            if ($data === false) {
                error_log("Erreur lors de la combinaison des données ligne $lineNumber");
                continue;
            }
            
            // Créer une circonscription seulement si nous avons les données minimales
            if (!empty($data['code_circonscription'] ?? '')) {
                $circo = new self($data);
                self::$circonscriptions[$circo->code] = $circo;
            }
        }
        
        fclose($handle);
        self::$loaded = true;
        
        error_log("Circonscriptions chargées : " . count(self::$circonscriptions));
    }

    public static function getAll() {
        self::loadFromCSV();
        return self::$circonscriptions;
    }

    public static function getByCode($code) {
        self::loadFromCSV();
        return self::$circonscriptions[$code] ?? null;
    }

    public function getDepartementNom() {
        $departements = [
            '01' => 'Ain', '02' => 'Aisne', '03' => 'Allier', '04' => 'Alpes-de-Haute-Provence',
            '05' => 'Hautes-Alpes', '06' => 'Alpes-Maritimes', '07' => 'Ardèche', '08' => 'Ardennes',
            '09' => 'Ariège', '10' => 'Aube', '11' => 'Aude', '12' => 'Aveyron',
            '13' => 'Bouches-du-Rhône', '14' => 'Calvados', '15' => 'Cantal', '16' => 'Charente',
            '17' => 'Charente-Maritime', '18' => 'Cher', '19' => 'Corrèze', '21' => 'Côte-d\'Or',
            '22' => 'Côtes-d\'Armor', '23' => 'Creuse', '24' => 'Dordogne', '25' => 'Doubs',
            '26' => 'Drôme', '27' => 'Eure', '28' => 'Eure-et-Loir', '29' => 'Finistère',
            '2A' => 'Corse-du-Sud', '2B' => 'Haute-Corse', '30' => 'Gard', '31' => 'Haute-Garonne',
            '32' => 'Gers', '33' => 'Gironde', '34' => 'Hérault', '35' => 'Ille-et-Vilaine',
            '36' => 'Indre', '37' => 'Indre-et-Loire', '38' => 'Isère', '39' => 'Jura',
            '40' => 'Landes', '41' => 'Loir-et-Cher', '42' => 'Loire', '43' => 'Haute-Loire',
            '44' => 'Loire-Atlantique', '45' => 'Loiret', '46' => 'Lot', '47' => 'Lot-et-Garonne',
            '48' => 'Lozère', '49' => 'Maine-et-Loire', '50' => 'Manche', '51' => 'Marne',
            '52' => 'Haute-Marne', '53' => 'Mayenne', '54' => 'Meurthe-et-Moselle', '55' => 'Meuse',
            '56' => 'Morbihan', '57' => 'Moselle', '58' => 'Nièvre', '59' => 'Nord',
            '60' => 'Oise', '61' => 'Orne', '62' => 'Pas-de-Calais', '63' => 'Puy-de-Dôme',
            '64' => 'Pyrénées-Atlantiques', '65' => 'Hautes-Pyrénées', '66' => 'Pyrénées-Orientales',
            '67' => 'Bas-Rhin', '68' => 'Haut-Rhin', '69' => 'Rhône', '70' => 'Haute-Saône',
            '71' => 'Saône-et-Loire', '72' => 'Sarthe', '73' => 'Savoie', '74' => 'Haute-Savoie',
            '75' => 'Paris', '76' => 'Seine-Maritime', '77' => 'Seine-et-Marne', '78' => 'Yvelines',
            '79' => 'Deux-Sèvres', '80' => 'Somme', '81' => 'Tarn', '82' => 'Tarn-et-Garonne',
            '83' => 'Var', '84' => 'Vaucluse', '85' => 'Vendée', '86' => 'Vienne',
            '87' => 'Haute-Vienne', '88' => 'Vosges', '89' => 'Yonne', '90' => 'Territoire de Belfort',
            '91' => 'Essonne', '92' => 'Hauts-de-Seine', '93' => 'Seine-Saint-Denis',
            '94' => 'Val-de-Marne', '95' => 'Val-d\'Oise'
        ];
        
        return $departements[$this->departement] ?? 'Inconnu';
    }

    public function convertKMLToSVG() {
        // Simplifié : convertit les coordonnées KML en format SVG
        if (empty($this->kmlShape)) return '';
        
        // Extraction basique des coordonnées du KML
        preg_match_all('/([0-9.-]+),([0-9.-]+)/', $this->kmlShape, $matches);
        
        if (count($matches[1]) < 3) return '';
        
        $points = [];
        $minLng = min($matches[1]);
        $maxLng = max($matches[1]);
        $minLat = min($matches[2]);
        $maxLat = max($matches[2]);
        
        // Éviter la division par zéro
        if ($maxLng == $minLng || $maxLat == $minLat) return '';
        
        // Normalisation pour SVG (0-1000)
        foreach ($matches[1] as $i => $lng) {
            $x = ($lng - $minLng) / ($maxLng - $minLng) * 1000;
            $y = (1 - (($matches[2][$i] - $minLat) / ($maxLat - $minLat))) * 600;
            $points[] = "$x,$y";
        }
        
        return implode(' ', $points);
    }
}
