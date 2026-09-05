document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('salleForm');
    if (!form) return;
    const nom = document.getElementById('nom');
    const etage = document.getElementById('etage');
    const capacite = document.getElementById('capacite');
    const batimentId = document.getElementById('batimentId');

    function setMsg(field, message, isValid) {
        let msg = field.parentElement.querySelector('.field-msg');
        if (!msg) { msg = document.createElement('div'); msg.className = 'field-msg'; field.parentElement.appendChild(msg); }
        msg.textContent = message;
        msg.className = 'field-msg ' + (isValid ? 'msg-success' : 'msg-error');
    }

    function checkNom() {
        const valid = nom.value.trim().length >= 3;
        setMsg(nom, valid ? 'Correct' : 'Le nom de la salle doit contenir au moins 3 caractères.', valid);
        return valid;
    }
    function checkEtage() {
        const val = parseInt(etage.value, 10);
        const valid = Number.isInteger(val) && val >= 0;
        setMsg(etage, valid ? 'Correct' : 'L\'étage doit être un entier positif ou nul.', valid);
        return valid;
    }
    function checkCapacite() {
        const val = parseInt(capacite.value, 10);
        const valid = Number.isInteger(val) && val >= 1;
        setMsg(capacite, valid ? 'Correct' : 'La capacité doit être un entier ≥ 1.', valid);
        return valid;
    }
    function checkBatiment() {
        const valid = batimentId.value !== '';
        setMsg(batimentId, valid ? 'Correct' : 'Choisissez un bâtiment.', valid);
        return valid;
    }

    nom.addEventListener('keyup', checkNom);
    etage.addEventListener('blur', checkEtage);
    capacite.addEventListener('blur', checkCapacite);
    batimentId.addEventListener('change', checkBatiment);

    form.addEventListener('submit', function (e) {
        const ok = [checkNom(), checkEtage(), checkCapacite(), checkBatiment()].every(Boolean);
        if (!ok) { e.preventDefault(); alert('Merci de corriger les erreurs du formulaire.'); }
    });
});
