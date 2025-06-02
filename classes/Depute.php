<?php
class Depute {
    public $slug;
    public $nom;
    public $prenom;
    public $sexe;
    public $date_naissance;
    public $groupe;

    public function __construct(array $data) {
        $this->nom = $data['etatCivil']['ident']['nom'] ?? '';
        $this->prenom = $data['etatCivil']['ident']['prenom'] ?? '';
        $this->slug = $data['uid']['acteurRef'] ?? '';
        $this->sexe = $data['etatCivil']['sexe'] ?? '';
        $this->date_naissance = $data['etatCivil']['dateNaissance'] ?? '';
        $this->groupe = $data['mandats'][0]['organesParlementaires'][0]['libelleAbrege'] ?? 'Inconnu';
    }
}