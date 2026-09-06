document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('batimentForm');
    if (!form) return;
    const nom = document.getElementById('nom');
    const adresse = document.getElementById('adresse');
    const etages = document.getElementById('etages');
    const latitude = document.getElementById('latitude');
    const longitude = document.getElementById('longitude');

    function setMsg(field, message, isValid) {
        let msg = field.parentElement.querySelector('.field-msg');
        if (!msg) { msg = document.createElement('div'); msg.className = 'field-msg'; field.parentElement.appendChild(msg); }
        msg.textContent = message;
        msg.className = 'field-msg ' + (isValid ? 'msg-success' : 'msg-error');
    }

    function checkNom() {
        const valid = nom.value.trim().length >= 3;
        setMsg(nom, valid ? 'Correct' : 'Le nom doit contenir au moins 3 caractères.', valid);
        return valid;
    }
    function checkAdresse() {
        const valid = adresse.value.trim().length >= 5;
        setMsg(adresse, valid ? 'Correct' : "L'adresse doit contenir au moins 5 caractères.", valid);
        return valid;
    }
    function checkEtages() {
        const val = parseInt(etages.value, 10);
        const valid = Number.isInteger(val) && val >= 1;
        setMsg(etages, valid ? 'Correct' : 'Le nombre d\'étages doit être un entier ≥ 1.', valid);
        return valid;
    }
    function checkPosition() {
        const valid = latitude.value !== '' && longitude.value !== '';
        setMsg(longitude, valid ? 'Position choisie' : 'Clique sur la carte pour placer le bâtiment.', valid);
        return valid;
    }

    nom.addEventListener('keyup', checkNom);
    adresse.addEventListener('blur', checkAdresse);
    etages.addEventListener('blur', checkEtages);

    form.addEventListener('submit', function (e) {
        const ok = [checkNom(), checkAdresse(), checkEtages(), checkPosition()].every(Boolean);
        if (!ok) { e.preventDefault(); alert('Merci de corriger les erreurs du formulaire (et de placer le bâtiment sur la carte).'); }
    });
});
