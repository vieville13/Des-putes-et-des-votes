
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('depute-modal');
    const modalBody = document.getElementById('modal-body');
    const closeModal = document.querySelector('.close');
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-commune');

    // Gestion des clics sur les circonscriptions
    document.querySelectorAll('.circonscription-item').forEach(item => {
        item.addEventListener('click', function() {
            const code = this.dataset.code;
            loadCirconscriptionInfo(code);
        });
    });

    // Fermeture de la modal
    closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Recherche par commune
    searchBtn.addEventListener('click', function() {
        const commune = searchInput.value.trim();
        if (commune) {
            searchByCommune(commune);
        }
    });

    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBtn.click();
        }
    });

    function loadCirconscriptionInfo(code) {
        modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="spinner"></div><p>Chargement...</p></div>';
        modal.style.display = 'block';

        fetch(`controllers/CarteController.php?action=getInfo&code=${encodeURIComponent(code)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    modalBody.innerHTML = `<div class="no-depute"><h3>❌ Erreur</h3><p>${data.error}</p></div>`;
                    return;
                }

                let html = `
                    <div class="depute-info">
                        <div class="circonscription-name">
                            ${data.circonscription.departement} - Circonscription ${data.circonscription.numero}
                        </div>
                `;

                if (data.depute) {
                    html += `
                        <h2>👤 ${data.depute.nom}</h2>
                        <div class="profession">${data.depute.profession}</div>
                        <a href="${data.depute.url}" class="view-profile">
                            📋 Voir le profil complet
                        </a>
                    `;
                } else {
                    html += `
                        <h2>🏛️ Circonscription sans député actuel</h2>
                        <p style="color: #7f8c8d;">Aucun député trouvé pour cette circonscription dans nos données actuelles.</p>
                    `;
                }

                html += '</div>';
                modalBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur:', error);
                modalBody.innerHTML = '<div class="no-depute"><h3>❌ Erreur</h3><p>Impossible de charger les informations.</p></div>';
            });
    }

    function searchByCommune(commune) {
        modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="spinner"></div><p>Recherche en cours...</p></div>';
        modal.style.display = 'block';

        // Pour l'instant, cette fonctionnalité n'est pas implémentée
        modalBody.innerHTML = `
            <div class="no-depute">
                <h3>🔍 Recherche par commune</h3>
                <p>Cette fonctionnalité sera bientôt disponible.</p>
                <p>Vous avez recherché : <strong>${commune}</strong></p>
                <p>En attendant, naviguez par département ci-dessous.</p>
            </div>
        `;
    }

    // Style du spinner
    const style = document.createElement('style');
    style.textContent = `
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
});
</script>
