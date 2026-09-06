document.addEventListener('DOMContentLoaded', function () {
    var latitude = document.getElementById('latitude');
    var longitude = document.getElementById('longitude');
    var initialLat = parseFloat(latitude.value) || 36.8065;
    var initialLng = parseFloat(longitude.value) || 10.1815;
    var map = L.map('map').setView([initialLat, initialLng], latitude.value ? 16 : 12);
    var marker;

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function setPosition(latlng) {
        latitude.value = latlng.lat.toFixed(7);
        longitude.value = latlng.lng.toFixed(7);
        if (marker) marker.setLatLng(latlng);
        else marker = L.marker(latlng, { draggable: true }).addTo(map);
        marker.on('dragend', function () { setPosition(marker.getLatLng()); });
    }

    if (latitude.value && longitude.value) setPosition({ lat: initialLat, lng: initialLng });
    map.on('click', function (event) { setPosition(event.latlng); });
});
