
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('depute-modal');
    const modalBody = document.getElementById('modal-body');
    const closeModal = document.querySelector('.close');

    // Fermeture de la modal
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    function loadCirconscriptionInfo(code) {
        modalBody.innerHTML = '<div style="text-align: center; padding: 40px;"><div class="spinner"></div><p>Chargement...</p></div>';
        modal.style.display = 'block';

        fetch(`index.php?action=getInfo&code=${encodeURIComponent(code)}`)
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

    // Style du spinner
    const style = document.createElement('style');
    style.textContent = `
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: none;
            border-radius: 12px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

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

        .depute-info {
            text-align: center;
        }

        .circonscription-name {
            background: #3498db;
            color: white;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .profession {
            color: #7f8c8d;
            font-style: italic;
            margin: 10px 0;
        }

        .view-profile {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
        }

        .view-profile:hover {
            background: #c0392b;
        }

        .no-depute {
            text-align: center;
            padding: 20px;
        }
    `;
    document.head.appendChild(style);

    // Initialisation de la carte Leaflet
    if (typeof L !== 'undefined') {
        // Créer la carte
        const map = L.map('map').setView([46.603354, 1.888334], 6);

        // Ajouter le fond de carte
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ajouter les circonscriptions si les données existent
        if (typeof circonscriptions !== 'undefined' && circonscriptions) {
            Object.values(circonscriptions).forEach(region => {
                if (region.circonscriptions) {
                    region.circonscriptions.forEach(circo => {
                        if (circo.kml_shape) {
                            try {
                                // Parser le KML pour extraire les coordonnées
                                const parser = new DOMParser();
                                const xmlDoc = parser.parseFromString(circo.kml_shape, "text/xml");
                                const coordinates = xmlDoc.getElementsByTagName('coordinates')[0];
                                
                                if (coordinates) {
                                    const coordsText = coordinates.textContent.trim();
                                    const coordPairs = coordsText.split(' ');
                                    const latLngs = [];
                                    
                                    coordPairs.forEach(pair => {
                                        const coords = pair.split(',');
                                        if (coords.length >= 2) {
                                            const lat = parseFloat(coords[1]);
                                            const lng = parseFloat(coords[0]);
                                            if (!isNaN(lat) && !isNaN(lng)) {
                                                latLngs.push([lat, lng]);
                                            }
                                        }
                                    });
                                    
                                    if (latLngs.length > 0) {
                                        // Créer un polygone
                                        const polygon = L.polygon(latLngs, {
                                            color: "#2980b9",
                                            weight: 2,
                                            fillOpacity: 0.3,
                                            fillColor: "#3498db"
                                        }).addTo(map);

                                        // Ajouter une popup
                                        polygon.bindPopup(`
                                            <strong>Circonscription ${circo.numero}</strong><br>
                                            Département: ${circo.departement}<br>
                                            Code: ${circo.code_circonscription}
                                        `);

                                        // Ajouter un événement de clic
                                        polygon.on('click', function() {
                                            loadCirconscriptionInfo(circo.code_circonscription);
                                        });
                                    }
                                }
                            } catch (error) {
                                console.error('Erreur lors du parsing KML pour la circonscription:', circo.code_circonscription, error);
                            }
                        }
                    });
                }
            });
        }
    } else {
        console.error('Leaflet n\'est pas chargé');
    }
});
