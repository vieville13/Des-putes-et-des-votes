
<?php
require_once __DIR__ . '/Circonscription.php';
require_once __DIR__ . '/Depute.php';

class CarteManager {
    private $circonscriptions = [];
    private $deputesPath;

    public function __construct() {
        $this->deputesPath = __DIR__ . '/../attached_assets/json/acteur/';
        $this->loadCirconscriptions();
    }

    private function loadCirconscriptions(): void {
        if (!is_dir($this->deputesPath)) {
            return;
        }

        $files = glob($this->deputesPath . '*.json');
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (isset($data['acteur'])) {
                $acteur = $data['acteur'];
                
                // Extraire les informations de circonscription des mandats
                $mandats = $acteur['mandats']['mandat'] ?? [];
                if (!is_array($mandats)) continue;

                foreach ($mandats as $mandat) {
                    if (isset($mandat['typeOrgane']) && $mandat['typeOrgane'] === 'ASSEMBLEE' && !isset($mandat['dateFin'])) {
                        $election = $mandat['election'] ?? [];
                        $lieu = $election['lieu'] ?? [];
                        
                        if (isset($lieu['numCirco'], $lieu['departement'], $lieu['numDepartement'])) {
                            $circonscriptionData = [
                                'numCirco' => $lieu['numCirco'],
                                'departement' => $lieu['departement'],
                                'numDepartement' => $lieu['numDepartement'],
                                'region' => $lieu['region'] ?? '',
                                'regionType' => $lieu['regionType'] ?? '',
                                'deputeUid' => $acteur['uid']['#text'] ?? '',
                                'deputeNom' => ($acteur['etatCivil']['ident']['prenom'] ?? '') . ' ' . ($acteur['etatCivil']['ident']['nom'] ?? '')
                            ];
                            
                            $circonscription = new Circonscription($circonscriptionData);
                            if ($circonscription->isValid()) {
                                $this->circonscriptions[$circonscription->getIdentifiant()] = $circonscription;
                            }
                        }
                        break;
                    }
                }
            }
        }
    }

    public function getAllCirconscriptions(): array {
        return $this->circonscriptions;
    }

    public function getCirconscriptionsByDepartement(string $numDepartement): array {
        return array_filter($this->circonscriptions, function($circonscription) use ($numDepartement) {
            return $circonscription->numDepartement === $numDepartement;
        });
    }

    public function getCirconscriptionsByRegion(string $region): array {
        return array_filter($this->circonscriptions, function($circonscription) use ($region) {
            return $circonscription->region === $region;
        });
    }

    public function getCirconscription(string $numDepartement, string $numCirco): ?Circonscription {
        $identifiant = $numDepartement . '-' . $numCirco;
        return $this->circonscriptions[$identifiant] ?? null;
    }

    public function searchCirconscriptions(string $query): array {
        $query = strtolower(trim($query));
        return array_filter($this->circonscriptions, function($circonscription) use ($query) {
            return str_contains(strtolower($circonscription->departement), $query)
                || str_contains(strtolower($circonscription->region), $query)
                || str_contains(strtolower($circonscription->deputeNom), $query)
                || str_contains($circonscription->numDepartement, $query);
        });
    }

    public function getDepartementsStats(): array {
        $stats = [];
        foreach ($this->circonscriptions as $circonscription) {
            $dep = $circonscription->numDepartement;
            if (!isset($stats[$dep])) {
                $stats[$dep] = [
                    'nom' => $circonscription->departement,
                    'region' => $circonscription->region,
                    'nombreCirconscriptions' => 0
                ];
            }
            $stats[$dep]['nombreCirconscriptions']++;
        }
        return $stats;
    }
}
