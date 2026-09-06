<footer>
    &copy; 2026 RoomBooking - Système de réservation de salles de réunion
</footer>

<!-- Fenêtre modale : position du bâtiment sur la carte -->
<div id="mapModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <button class="modal-close" type="button" onclick="closeMapModal()">&times;</button>
        <h3 id="mapModalTitle">Bâtiment</h3>
        <p class="modal-sub" id="mapModalSub"></p>
        <div id="mapModalMap"></div>
    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="assets/js/mapModal.js"></script>
