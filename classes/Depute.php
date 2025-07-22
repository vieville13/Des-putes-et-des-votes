
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
            $lieu = isset($lieu['#text']) ? $lieu['#text'] : (is_string($lieu[0]) ? $lieu[0] : '');
        }
        
        $dep = $infoNaissance['depNais'] ?? '';
        if (is_array($dep)) {
            $dep = isset($dep['#text']) ? $dep['#text'] : (is_string($dep[0]) ? $dep[0] : '');
        }
        
        $pays = $infoNaissance['paysNais'] ?? '';
        if (is_array($pays)) {
            $pays = isset($pays['#text']) ? $pays['#text'] : (is_string($pays[0]) ? $pays[0] : '');
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
                if (isset($mandat['organes']['organeRef'])) {
                    if (is_array($mandat['organes']['organeRef'])) {
                        $organe = implode(', ', $mandat['organes']['organeRef']);
                    } else {
                        $organe = $mandat['organes']['organeRef'];
                    }
                }
                
                $this->mandats[] = [
                    'type' => $mandat['typeOrgane'],
                    'dateDebut' => $mandat['dateDebut'] ?? '',
                    'dateFin' => $mandat['dateFin'] ?? null,
                    'qualite' => $mandat['infosQualite']['libQualite'] ?? '',
                    'organe' => $organe
                ];
            }
        }
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
