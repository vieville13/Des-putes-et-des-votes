
<?php
require_once __DIR__ . '/../classes/Depute.php';

class DeputeController {
    private $deputes = [];
    private $deputesPath;

    public function __construct() {
        $this->deputesPath = __DIR__ . '/../attached_assets/json/acteur/';
        $this->loadDeputes();
    }

    private function loadDeputes() {
        if (!is_dir($this->deputesPath)) {
            return;
        }

        $files = glob($this->deputesPath . '*.json');
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

    public function searchDeputes(string $query): array {
        $query = strtolower(trim($query));
        return array_filter($this->deputes, function($depute) use ($query) {
            return str_contains(strtolower($depute->nom), $query)
                || str_contains(strtolower($depute->prenom), $query)
                || str_contains(strtolower($depute->uid), $query);
        });
    }

    public function getAll(): array {
        return $this->deputes;
    }

    public function getByUid(string $uid): ?Depute {
        return $this->deputes[$uid] ?? null;
    }

    public function show(string $uid): void {
        $depute = $this->getByUid($uid);
        if (!$depute) {
            echo "<p>Député introuvable.</p>";
            return;
        }

        require __DIR__ . '/../views/depute_view.php';
    }
}
