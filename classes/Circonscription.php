
<?php
class Circonscription {
    public $numero;
    public $departement;
    public $numDepartement;
    public $region;
    public $regionType;
    public $deputeUid;
    public $deputeNom;
    public $coordonnees;

    public function __construct(array $data = []) {
        $this->numero = $data['numCirco'] ?? '';
        $this->departement = $data['departement'] ?? '';
        $this->numDepartement = $data['numDepartement'] ?? '';
        $this->region = $data['region'] ?? '';
        $this->regionType = $data['regionType'] ?? '';
        $this->deputeUid = $data['deputeUid'] ?? '';
        $this->deputeNom = $data['deputeNom'] ?? '';
        $this->coordonnees = $data['coordonnees'] ?? [];
    }

    public function getIdentifiant(): string {
        return $this->numDepartement . '-' . $this->numero;
    }

    public function getNomComplet(): string {
        return $this->departement . ' - ' . $this->numero . 'ème circonscription';
    }

    public function isValid(): bool {
        return !empty($this->numero) && !empty($this->departement) && !empty($this->numDepartement);
    }
}
