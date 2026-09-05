document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reservationForm');
    if (!form) return;

    const objet = document.getElementById('objet');
    const dateDebut = document.getElementById('dateDebut');
    const dateFin = document.getElementById('dateFin');

    function setMsg(field, message, isValid) {
        let msg = field.parentElement.querySelector('.field-msg');
        if (!msg) { msg = document.createElement('div'); msg.className = 'field-msg'; field.parentElement.appendChild(msg); }
        msg.textContent = message;
        msg.className = 'field-msg ' + (isValid ? 'msg-success' : 'msg-error');
    }

    function checkObjet() {
        const valid = objet.value.trim().length >= 3;
        setMsg(objet, valid ? 'Correct' : "L'objet doit contenir au moins 3 caractères.", valid);
        return valid;
    }

    function checkDates() {
        if (!dateDebut.value || !dateFin.value) {
            setMsg(dateFin, 'Les dates de début et de fin sont obligatoires.', false);
            return false;
        }
        const debut = new Date(dateDebut.value);
        const fin = new Date(dateFin.value);
        const now = new Date();

        if (debut <= now) {
            setMsg(dateDebut, 'La date de début doit être dans le futur.', false);
            return false;
        }
        setMsg(dateDebut, 'Correct', true);

        const valid = fin > debut;
        setMsg(dateFin, valid ? 'Correct' : 'La date de fin doit être après la date de début.', valid);
        return valid;
    }

    objet.addEventListener('keyup', checkObjet);
    dateDebut.addEventListener('change', checkDates);
    dateFin.addEventListener('change', checkDates);

    form.addEventListener('submit', function (e) {
        const ok = [checkObjet(), checkDates()].every(Boolean);
        if (!ok) { e.preventDefault(); alert('Merci de corriger les erreurs du formulaire.'); }
    });
});
