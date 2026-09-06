let mapModalInstance = null;
let mapModalMarker = null;

function openMapModal(nom, adresse, lat, lng) {
    lat = parseFloat(lat);
    lng = parseFloat(lng);

    document.getElementById('mapModalTitle').textContent = nom;
    document.getElementById('mapModalSub').textContent = adresse || '';
    document.getElementById('mapModalOverlay').style.display = 'flex';

    // Leaflet a besoin que le conteneur soit visible avant de calculer sa taille
    setTimeout(function () {
        if (!mapModalInstance) {
            mapModalInstance = L.map('mapModalMap').setView([lat, lng], 16);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapModalInstance);
            mapModalMarker = L.marker([lat, lng]).addTo(mapModalInstance);
        } else {
            mapModalInstance.setView([lat, lng], 16);
            mapModalMarker.setLatLng([lat, lng]);
        }
        mapModalMarker.bindPopup(nom).openPopup();
        mapModalInstance.invalidateSize();
    }, 50);
}

function closeMapModal() {
    document.getElementById('mapModalOverlay').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('mapModalOverlay');
    if (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeMapModal();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMapModal();
    });
});
