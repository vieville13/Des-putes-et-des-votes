
<?php
class Depute {
    public $uid;
    public $nom;
    public $prenom;
    public $sexe;
    public $dateNaissance;
    public $lieuNaissance;
    public $profession;
    public $email;
    public $trigramme;
    public $uriHatvp;
    public $mandats = [];
    public $collaborateurs = [];
    public $adresses = [];

    public function __construct(array $data) {
        $this->uid = $data['uid']['#text'] ?? '';
        $this->nom = $data['etatCivil']['ident']['nom'] ?? '';
        $this->prenom = $data['etatCivil']['ident']['prenom'] ?? '';
        $this->sexe = $data['etatCivil']['ident']['civ'] ?? '';
        $this->trigramme = $data['etatCivil']['ident']['trigramme'] ?? '';
        
        // Informations de naissance
        $this->dateNaissance = $data['etatCivil']['infoNaissance']['dateNais'] ?? '';
        $this->lieuNaissance = $this->formatLieuNaissance($data['etatCivil']['infoNaissance'] ?? []);
        
        // Profession
        $this->profession = $data['profession']['libelleCourant'] ?? '';
        
        // URI HATVP
        $this->uriHatvp = $data['uri_hatvp'] ?? '';
        
        // Adresses
        $this->loadAdresses($data['adresses']['adresse'] ?? []);
        
        // Mandats
        $this->loadMandats($data['mandats']['mandat'] ?? []);
        
        // Collaborateurs
        $this->loadCollaborateurs($data['collaborateurs']['collaborateur'] ?? []);
    }

    private function formatLieuNaissance(array $infoNaissance): string {
        // Gérer le cas où les champs peuvent être des tableaux
        $lieu = $infoNaissance['villeNais'] ?? '';
        if (is_array($lieu)) {
            if (isset($lieu['#text'])) {
                $lieu = $lieu['#text'];
            } elseif (!empty($lieu) && isset($lieu[0]) && is_string($lieu[0])) {
                $lieu = $lieu[0];
            } else {
                $lieu = '';
            }
        }
        
        $dep = $infoNaissance['depNais'] ?? '';
        if (is_array($dep)) {
            if (isset($dep['#text'])) {
                $dep = $dep['#text'];
            } elseif (!empty($dep) && isset($dep[0]) && is_string($dep[0])) {
                $dep = $dep[0];
            } else {
                $dep = '';
            }
        }
        
        $pays = $infoNaissance['paysNais'] ?? '';
        if (is_array($pays)) {
            if (isset($pays['#text'])) {
                $pays = $pays['#text'];
            } elseif (!empty($pays) && isset($pays[0]) && is_string($pays[0])) {
                $pays = $pays[0];
            } else {
                $pays = '';
            }
        }
        
        if ($lieu && $dep && $pays === 'France') {
            return "$lieu ($dep)";
        } elseif ($lieu && $pays) {
            return "$lieu, $pays";
        }
        return $lieu;
    }

    private function loadAdresses(array $adresses): void {
        if (!is_array($adresses)) return;
        
        foreach ($adresses as $adresse) {
            if (isset($adresse['type'])) {
                switch ($adresse['type']) {
                    case '15':
                        $this->email = $adresse['valElec'] ?? '';
                        break;
                    default:
                        $this->adresses[] = $adresse;
                }
            }
        }
    }

    private function loadMandats(array $mandats): void {
        if (!is_array($mandats)) return;
        
        foreach ($mandats as $mandat) {
            if (isset($mandat['typeOrgane'])) {
                // Gérer le cas où organes peut être un tableau ou une chaîne
                $organe = '';
                $organeRef = '';
                if (isset($mandat['organes']['organeRef'])) {
                    if (is_array($mandat['organes']['organeRef'])) {
                        $organe = implode(', ', $mandat['organes']['organeRef']);
                        $organeRef = $mandat['organes']['organeRef'][0];
                    } else {
                        $organe = $mandat['organes']['organeRef'];
                        $organeRef = $mandat['organes']['organeRef'];
                    }
                }
                
                // Pour les partis politiques et groupes parlementaires, essayer de récupérer le nom complet
                $qualiteAffichage = $mandat['infosQualite']['libQualite'] ?? '';
                if (($mandat['typeOrgane'] === 'PARPOL' || $mandat['typeOrgane'] === 'GP') && $organeRef) {
                    $nomOrgane = $this->getOrganeLibelle($organeRef);
                    if ($nomOrgane) {
                        $qualiteAffichage = $nomOrgane;
                    }
                }
                
                $this->mandats[] = [
                    'type' => $mandat['typeOrgane'],
                    'dateDebut' => $mandat['dateDebut'] ?? '',
                    'dateFin' => $mandat['dateFin'] ?? null,
                    'qualite' => $qualiteAffichage,
                    'organe' => $organe,
                    'organeRef' => $organeRef
                ];
            }
        }
    }

    private function getOrganeLibelle(string $organeRef): ?string {
        $organePath = __DIR__ . '/../attached_assets/json/organe/' . $organeRef . '.json';
        if (file_exists($organePath)) {
            $organeData = json_decode(file_get_contents($organePath), true);
            if (isset($organeData['organe']['libelleAbrev'])) {
                return $organeData['organe']['libelleAbrev'];
            }
            if (isset($organeData['organe']['libelle'])) {
                return $organeData['organe']['libelle'];
            }
        }
        return null;
    }

    private function loadCollaborateurs(array $collaborateurs): void {
        if (!is_array($collaborateurs)) return;
        
        foreach ($collaborateurs as $collab) {
            $this->collaborateurs[] = [
                'prenom' => $collab['etatCivil']['ident']['prenom'] ?? '',
                'nom' => $collab['etatCivil']['ident']['nom'] ?? '',
                'civilite' => $collab['etatCivil']['ident']['civ'] ?? ''
            ];
        }
    }

    public function isValid(): bool {
        return !empty($this->nom) && !empty($this->prenom) && !empty($this->uid);
    }

    public function getNomComplet(): string {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getMandatPrincipal(): ?array {
        foreach ($this->mandats as $mandat) {
            if ($mandat['type'] === 'ASSEMBLEE') {
                return $mandat;
            }
        }
        return null;
    }

    public function getNombreMandats(): int {
        return count($this->mandats);
    }
}
