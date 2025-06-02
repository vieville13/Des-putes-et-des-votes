<?php
require_once __DIR__ . '/../classes/Depute.php';

class DeputeController {
    private $deputes = [];

    public function __construct() {
        $jsonFile = __DIR__ . '/../data/AMO30-legislature-16-composite.json';
        $data = json_decode(file_get_contents($jsonFile), true);

        foreach ($data['acteurs'] as $acteur) {
            if (($acteur['etatCivil']['ident']['qualite'] ?? '') === 'Député') {
                $depute = new Depute($acteur);
                $this->deputes[$depute->slug] = $depute;
            }
        }
    }

    public function searchDeputes(string $query): array {
        $query = strtolower(trim($query));
        return array_filter($this->deputes, function($depute) use ($query) {
            return str_contains(strtolower($depute->nom), $query)
                || str_contains(strtolower($depute->prenom), $query)
                || str_contains(strtolower($depute->slug), $query);
        });
    }

    public function show(string $slug): void {
        $depute = $this->deputes[$slug] ?? null;
        if (!$depute) {
            echo "<p>Député introuvable.</p>";
            return;
        }

        echo "<h1>{$depute->prenom} {$depute->nom}</h1>";
        echo "<p>Sexe : {$depute->sexe}</p>";
        echo "<p>Date de naissance : {$depute->date_naissance}</p>";
        echo "<p>Groupe : {$depute->groupe}</p>";
        echo "<p><a href='index.php'>← Retour à la recherche</a></p>";
    }
}
