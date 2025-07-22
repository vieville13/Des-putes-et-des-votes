
// JavaScript pour la carte interactive
class CarteInteractive {
    constructor() {
        this.circonscriptions = window.circonscriptionsData || [];
        this.departementsStats = window.departementsStats || {};
        this.selectedDepartement = null;
        this.filteredResults = [];
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.populateFilters();
        this.renderCarte();
        this.showAllCirconscriptions();
    }

    setupEventListeners() {
        // Recherche
        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        
        if (searchInput && searchBtn) {
            searchBtn.addEventListener('click', () => this.performSearch());
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.performSearch();
            });
        }

        // Filtres
        const regionFilter = document.getElementById('regionFilter');
        const departementFilter = document.getElementById('departementFilter');
        
        if (regionFilter) {
            regionFilter.addEventListener('change', () => this.applyFilters());
        }
        
        if (departementFilter) {
            departementFilter.addEventListener('change', () => this.applyFilters());
        }
    }

    populateFilters() {
        const regions = [...new Set(this.circonscriptions.map(c => c.region))].sort();
        const departements = Object.values(this.departementsStats).sort((a, b) => a.nom.localeCompare(b.nom));

        // Remplir le filtre des régions
        const regionFilter = document.getElementById('regionFilter');
        if (regionFilter) {
            regions.forEach(region => {
                if (region) {
                    const option = document.createElement('option');
                    option.value = region;
                    option.textContent = region;
                    regionFilter.appendChild(option);
                }
            });
        }

        // Remplir le filtre des départements
        const departementFilter = document.getElementById('departementFilter');
        if (departementFilter) {
            departements.forEach(dep => {
                const option = document.createElement('option');
                option.value = dep.nom;
                option.textContent = `${dep.nom} (${dep.nombreCirconscriptions})`;
                departementFilter.appendChild(option);
            });
        }
    }

    renderCarte() {
        const carteContainer = document.getElementById('carteInteractive');
        if (!carteContainer) return;

        // Pour cette démonstration, on crée une liste de départements cliquables
        // Dans une vraie implémentation, on utiliserait SVG avec les contours de la France
        carteContainer.innerHTML = '<div class="carte-placeholder">Carte de France interactive<br><small>Cliquez sur un département ci-dessous :</small></div>';
        
        const departementsContainer = document.createElement('div');
        departementsContainer.className = 'departements-grid';
        departementsContainer.style.cssText = `
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
            padding: 10px;
        `;

        // Créer les boutons de départements
        Object.entries(this.departementsStats).forEach(([numDep, stats]) => {
            const depButton = document.createElement('button');
            depButton.className = 'departement-button';
            depButton.style.cssText = `
                padding: 10px;
                border: 2px solid #3498db;
                background: white;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s;
                font-size: 12px;
                text-align: center;
            `;
            depButton.innerHTML = `<strong>${numDep}</strong><br>${stats.nom}<br><small>${stats.nombreCirconscriptions} circ.</small>`;
            
            depButton.addEventListener('click', () => this.selectDepartement(numDep, stats));
            depButton.addEventListener('mouseenter', () => {
                depButton.style.background = '#3498db';
                depButton.style.color = 'white';
            });
            depButton.addEventListener('mouseleave', () => {
                if (this.selectedDepartement !== numDep) {
                    depButton.style.background = 'white';
                    depButton.style.color = 'inherit';
                }
            });
            
            departementsContainer.appendChild(depButton);
        });

        carteContainer.appendChild(departementsContainer);
    }

    selectDepartement(numDep, stats) {
        this.selectedDepartement = numDep;
        
        // Mettre à jour l'affichage des boutons
        document.querySelectorAll('.departement-button').forEach(btn => {
            btn.style.background = 'white';
            btn.style.color = 'inherit';
        });
        
        event.target.style.background = '#e74c3c';
        event.target.style.color = 'white';

        // Afficher les informations du département
        this.showDepartementInfo(numDep, stats);
        
        // Afficher les circonscriptions du département
        this.showCirconscriptionsByDepartement(numDep);
    }

    showDepartementInfo(numDep, stats) {
        const infoPanel = document.getElementById('infoPanel');
        if (!infoPanel) return;

        infoPanel.innerHTML = `
            <h3>Département ${numDep}</h3>
            <div style="margin-bottom: 15px;">
                <strong>Nom :</strong> ${stats.nom}<br>
                <strong>Région :</strong> ${stats.region}<br>
                <strong>Circonscriptions :</strong> ${stats.nombreCirconscriptions}
            </div>
            <button onclick="carteInstance.showAllCirconscriptions()" style="
                padding: 8px 15px;
                background: #95a5a6;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            ">Voir toutes les circonscriptions</button>
        `;
    }

    showCirconscriptionsByDepartement(numDep) {
        const circonscriptions = this.circonscriptions.filter(c => c.numDepartement === numDep);
        this.displayResults(circonscriptions, `Circonscriptions du département ${numDep}`);
    }

    showAllCirconscriptions() {
        this.selectedDepartement = null;
        
        // Réinitialiser l'affichage des boutons
        document.querySelectorAll('.departement-button').forEach(btn => {
            btn.style.background = 'white';
            btn.style.color = 'inherit';
        });

        const infoPanel = document.getElementById('infoPanel');
        if (infoPanel) {
            infoPanel.innerHTML = `
                <h3>Informations</h3>
                <p>Cliquez sur un département pour voir ses circonscriptions</p>
            `;
        }

        this.displayResults(this.circonscriptions, 'Toutes les circonscriptions');
    }

    performSearch() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) {
            this.showAllCirconscriptions();
            return;
        }

        const results = this.circonscriptions.filter(c => 
            c.departement.toLowerCase().includes(query.toLowerCase()) ||
            c.region.toLowerCase().includes(query.toLowerCase()) ||
            c.deputeNom.toLowerCase().includes(query.toLowerCase()) ||
            c.numDepartement.includes(query)
        );

        this.displayResults(results, `Résultats pour "${query}"`);
    }

    applyFilters() {
        const regionFilter = document.getElementById('regionFilter').value;
        const departementFilter = document.getElementById('departementFilter').value;

        let filtered = this.circonscriptions;

        if (regionFilter) {
            filtered = filtered.filter(c => c.region === regionFilter);
        }

        if (departementFilter) {
            filtered = filtered.filter(c => c.departement === departementFilter);
        }

        const title = regionFilter || departementFilter ? 'Résultats filtrés' : 'Toutes les circonscriptions';
        this.displayResults(filtered, title);
    }

    displayResults(circonscriptions, title = 'Résultats') {
        const resultsPanel = document.getElementById('resultsPanel');
        if (!resultsPanel) return;

        const resultsList = document.getElementById('resultsList');
        if (!resultsList) return;

        resultsPanel.querySelector('h3').textContent = `${title} (${circonscriptions.length})`;

        resultsList.innerHTML = '';

        circonscriptions.forEach(circ => {
            const item = document.createElement('div');
            item.className = 'result-item';
            item.innerHTML = `
                <strong>${circ.departement} - ${circ.numero}ème circonscription</strong>
                <div style="margin: 5px 0;">
                    <strong>Député :</strong> ${circ.deputeNom || 'Non renseigné'}
                </div>
                <small>
                    Région : ${circ.region}<br>
                    Département : ${circ.numDepartement}
                </small>
            `;
            
            item.addEventListener('click', () => {
                if (circ.deputeUid) {
                    window.location.href = `?uid=${circ.deputeUid}`;
                }
            });

            resultsList.appendChild(item);
        });

        if (circonscriptions.length === 0) {
            resultsList.innerHTML = '<p style="text-align: center; color: #7f8c8d; font-style: italic;">Aucun résultat trouvé</p>';
        }
    }
}

// Initialiser la carte quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.carteInstance = new CarteInteractive();
});
